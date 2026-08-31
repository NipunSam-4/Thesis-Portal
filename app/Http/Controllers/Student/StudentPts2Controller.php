<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pts2Form;
use App\Models\Thesis;
use App\Services\PtsDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPts2Controller extends Controller
{
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }
    // Display the PTS-2 creation form.
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'approved')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'PTS-2 Synopsis Form is locked until your PTS-1 Form is fully approved by DOAA.');
        }

        // Must be within the 15-day Open Seminar deadline (or approved extension deadline)
        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('warning', 'The PTS-2 submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'the deadline') . '. Please apply for a PTS-2 extension if eligible.');
        }

        $pts2Form = $thesis->pts2Form;
        if ($pts2Form) {
            if ($pts2Form->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-2 form is currently under review.');
            }
            if ($pts2Form->status === 'approved') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-2 form has already been approved.');
            }
            if ($pts2Form->status === 'reverted') {
                return redirect()->route('student.pts2.edit')->with('warning', 'You have a reverted PTS-2 form. Please edit and resubmit your reverted form.');
            }
            if ($pts2Form->status === 'rejected') {
                $pts2Form = null;
            }
        }

        return view('student.pts2.create', compact('user', 'student', 'thesis', 'pts2Form'));
    }

    // Display the PTS-2 edit form for reverted submissions.
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'No active registered thesis found.');
        }

        $pts2Form = $thesis->pts2Form;
        if (!$pts2Form || $pts2Form->status !== 'reverted') {
            return redirect()->route('student.dashboard')->with('warning', 'You do not have a reverted PTS-2 form to edit.');
        }

        return view('student.pts2.create', compact('user', 'student', 'thesis', 'pts2Form'));
    }

    // Store a newly created / resubmitted PTS-2 submission.
    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form', 'pts2Extension'])->firstOrFail();

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized: PTS-1 is not approved.');
        }

        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('error', 'Cannot submit PTS-2: the submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'N/A') . '.');
        }

        $pts2Form = $thesis->pts2Form;
        $hasExisting = $pts2Form && in_array($pts2Form->status, ['reverted', 'rejected']);
        $fileRequired = $hasExisting && $pts2Form->synopsis_report_doc_path ? 'nullable' : 'required';

        $validated = $request->validate([
            'thesis_title' => 'required|string|max:1000',
            'current_address' => 'required|string|max:1000',
            'alternate_email' => 'nullable|email|max:255',
            'recent_phone_country_code' => 'required|string|max:5',
            'recent_phone_number' => 'required|digits_between:5,15',
            'recent_phone_iso2' => 'required|string|max:10',
            'alternate_phone_country_code' => 'nullable|string|max:5',
            'alternate_phone_number' => 'nullable|digits_between:5,15',
            'alternate_phone_iso2' => 'nullable|string|max:10',
            'course_credits_student' => 'required|numeric|min:0',
            'cert_prima_facie_case' => 'required|accepted',
            'cert_no_prior_degree_submission' => 'required|accepted',
            'collaborative_work_status' => 'required|boolean',
            'collaborative_work_details' => $request->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
            'student_declaration' => 'required|accepted',
            'synopsis_report_doc' => "{$fileRequired}|file|mimes:pdf,doc,docx|max:10240",
        ]);

        // Update active thesis title
        $thesis->update(['title' => $validated['thesis_title']]);

        if ($request->hasFile('synopsis_report_doc')) {
            $filePath = $this->ptsDocService->storeInProgressDocument($request->file('synopsis_report_doc'), $student->roll_number, $thesis->id, 'pts2', 'Synopsis_Report', 'Student');
        } else {
            $filePath = $hasExisting ? $this->ptsDocService->copyExistingToInProgress($pts2Form->synopsis_report_doc_path, $student->roll_number, $thesis->id, 'pts2', 'Synopsis_Report', 'Student') : null;
        }

        $pts1 = $thesis->pts1Form;
        $coSupervisors = $student->allCoSupervisors()->pluck('id')->all();

        // Initialize co-supervisor slot mapping
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $pts1 ? $pts1->$col : ($coSupervisors[$i - 1] ?? null);
        }

        $formData = array_merge([
            'thesis_id' => $thesis->id,
            'thesis_title' => $validated['thesis_title'],
            'synopsis_report_doc_path' => $filePath,
            'current_stage' => 'main_supervisor',
            'status' => 'in_progress',
            'date_of_submission' => now()->toDateString(),
            'course_credits_student' => (float)$validated['course_credits_student'],
            'current_address' => $validated['current_address'],
            'alternate_email' => $validated['alternate_email'] ?? null,
            'recent_phone_number' => $validated['recent_phone_number'],
            'recent_phone_country_code' => $validated['recent_phone_country_code'],
            'recent_phone_iso2' => $validated['recent_phone_iso2'],
            'alternate_phone_number' => $validated['alternate_phone_number'] ?? null,
            'alternate_phone_country_code' => $validated['alternate_phone_country_code'] ?? null,
            'alternate_phone_iso2' => $validated['alternate_phone_iso2'] ?? null,
            'cert_prima_facie_case' => true,
            'cert_no_prior_degree_submission' => true,
            'collaborative_work_status' => $request->boolean('collaborative_work_status'),
            'collaborative_work_details' => $request->boolean('collaborative_work_status') ? $validated['collaborative_work_details'] : null,
        ], $coSupData);

        // Always create a new PTS-2 form row, preserving historical reverted/rejected submissions
        Pts2Form::create($formData);

        $actionVerb = $hasExisting ? 'resubmitted' : 'submitted';

        return redirect()->route('student.dashboard')->with('success', "PTS-2 Synopsis Form {$actionVerb} successfully and forwarded to your Main Supervisor for review.");
    }
}
