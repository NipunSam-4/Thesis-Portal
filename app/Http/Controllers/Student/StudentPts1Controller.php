<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pts1Form;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentPts1Controller extends Controller
{
    /**
     * Display the PTS-1 creation form.
     */
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Check if student has an active thesis with status in_progress
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $pts1Form = $thesis->pts1Form;
        if ($pts1Form) {
            if ($pts1Form->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form is currently in progress.');
            }
            if ($pts1Form->status === 'accepted') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form has already been approved.');
            }
        }

        return view('student.pts1.create', compact('user', 'student', 'thesis', 'pts1Form'));
    }

    /**
     * Download the sample Excel publication list template.
     */
    public function downloadTemplate()
    {
        $customTemplatePath = public_path('templates/publication_list_template.xlsx');
        
        if (file_exists($customTemplatePath)) {
            return response()->download($customTemplatePath);
        }

        $xlsTemplatePath = public_path('templates/publication_list_template.xls');
        if (file_exists($xlsTemplatePath)) {
            return response()->download($xlsTemplatePath);
        }

        abort(404, 'Publication list template file not found.');
    }

    /**
     * Display the PTS-1 edit form for reverted submissions.
     */
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Check if student has an active thesis with status in_progress
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $pts1Form = $thesis->pts1Form;
        if (!$pts1Form || $pts1Form->status !== 'reverted') {
            return redirect()->route('student.dashboard')->with('warning', 'You do not have a reverted PTS-1 form to edit.');
        }

        return view('student.pts1.edit', compact('user', 'student', 'thesis', 'pts1Form'));
    }

    /**
     * Store a newly created PTS-1 submission in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Fetch the student's registered active thesis from the theses table
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('error', 'Please register your thesis title on your dashboard first before submitting PTS-1.');
        }

        $pts1Form = $thesis->pts1Form;
        $hasExisting = $pts1Form && $pts1Form->status === 'reverted';

        // Standard default php.ini file upload limit: 10 MB (10240 KB)
        $validated = $request->validate([
            'thesis_title' => 'required|string|max:1000',
            'date_confirmation' => 'required|date',
            'seminar_date' => 'required|date',
            'seminar_time' => 'required|string|max:100',
            'seminar_venue' => 'required|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            
            'publication_norm_fulfillment' => 'required|boolean',
            'special_approval_publication' => 'nullable|boolean',
            'publication_approval_doc' => [
                'nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048',
                Rule::requiredIf(function() use ($request, $hasExisting, $pts1Form) {
                    if ($request->boolean('publication_norm_fulfillment') || !$request->boolean('special_approval_publication')) {
                        return false;
                    }
                    return !$hasExisting || !$pts1Form->publication_approval_doc_path;
                })
            ],

            'min_time_req_fulfilled' => 'required|boolean',
            'special_approval_min_time' => 'nullable|boolean',
            'min_time_approval_doc' => [
                'nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048',
                Rule::requiredIf(function() use ($request, $hasExisting, $pts1Form) {
                    if ($request->boolean('min_time_req_fulfilled') || !$request->boolean('special_approval_min_time')) {
                        return false;
                    }
                    return !$hasExisting || !$pts1Form->min_time_approval_doc_path;
                })
            ],

            'draft_synopsis_report' => [
                'file', 'mimes:pdf,docx', 'max:10240',
                $hasExisting && $pts1Form->draft_synopsis_report_doc_path ? 'nullable' : 'required'
            ],
            'publication_list' => [
                'file', 'mimes:xlsx,xls', 'max:2048',
                $hasExisting && $pts1Form->publication_list_doc_path ? 'nullable' : 'required'
            ],
        ]);

        $pubNormFulfilled = $request->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? false : $request->boolean('special_approval_publication');

        $minTimeFulfilled = $request->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? false : $request->boolean('special_approval_min_time');

        // Guard validation: If norm/min-time is false and special approval is false, reject
        if (!$pubNormFulfilled && !$pubSpecialApproval) {
            return back()->withInput()->withErrors(['special_approval_publication' => 'Special approval is required when publication norm criteria is not fulfilled.']);
        }

        if (!$minTimeFulfilled && !$minTimeSpecialApproval) {
            return back()->withInput()->withErrors(['special_approval_min_time' => 'Special approval is required when minimum time requirement criteria is not fulfilled.']);
        }

        // Update student confirmation date
        $student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Update thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        // Handle private local file uploads (storage/app/private/pts1_documents/)
        $pubAppDocPath = null;
        if (!$pubNormFulfilled && $pubSpecialApproval) {
            if ($request->hasFile('publication_approval_doc')) {
                $pubAppDocPath = $request->file('publication_approval_doc')->store('private/pts1_documents', 'local');
            } elseif ($hasExisting) {
                $pubAppDocPath = $pts1Form->publication_approval_doc_path;
            }
        }

        $minTimeAppDocPath = null;
        if (!$minTimeFulfilled && $minTimeSpecialApproval) {
            if ($request->hasFile('min_time_approval_doc')) {
                $minTimeAppDocPath = $request->file('min_time_approval_doc')->store('private/pts1_documents', 'local');
            } elseif ($hasExisting) {
                $minTimeAppDocPath = $pts1Form->min_time_approval_doc_path;
            }
        }

        if ($request->hasFile('draft_synopsis_report')) {
            $synopsisPath = $request->file('draft_synopsis_report')->store('private/pts1_documents', 'local');
        } else {
            $synopsisPath = $pts1Form->draft_synopsis_report_doc_path;
        }

        if ($request->hasFile('publication_list')) {
            $pubListPath = $request->file('publication_list')->store('private/pts1_documents', 'local');
        } else {
            $pubListPath = $pts1Form->publication_list_doc_path;
        }

        // Committee Co-Supervisors & PSPC IDs
        $coSupervisors = $student->coSupervisors()->pluck('users.id')->all();
        $pspcMembers = $student->pspcMembers()->pluck('users.id')->all();

        $formData = [
            'thesis_id' => $thesis->id,
            'seminar_date' => $validated['seminar_date'],
            'seminar_time' => $validated['seminar_time'],
            'seminar_venue' => $validated['seminar_venue'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'publication_norm_fulfillment' => $pubNormFulfilled,
            'special_approval_publication' => $pubSpecialApproval,
            'publication_approval_doc_path' => $pubAppDocPath,
            'min_time_req_fulfilled' => $minTimeFulfilled,
            'special_approval_min_time' => $minTimeSpecialApproval,
            'min_time_approval_doc_path' => $minTimeAppDocPath,
            'draft_synopsis_report_doc_path' => $synopsisPath,
            'publication_list_doc_path' => $pubListPath,
            'current_stage' => 'main_supervisor',
            'status' => 'in_progress',
        ];

        // Dynamically assign up to 10 Co-Supervisors and 10 PSPC Members
        for ($i = 1; $i <= 10; $i++) {
            $formData["co_supervisor_{$i}_id"] = $coSupervisors[$i - 1] ?? null;
            $formData["pspc_member_{$i}_id"] = $pspcMembers[$i - 1] ?? null;
        }

        // Create new active PTS-1 Form
        Pts1Form::create($formData);

        return redirect()->route('student.dashboard')->with('success', 'PTS-1 form resubmitted successfully and forwarded to your Main Supervisor for review!');
    }
}
