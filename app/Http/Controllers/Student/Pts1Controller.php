<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pts1Form;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Pts1Controller extends Controller
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

        // Check if student already has a thesis with a PTS-1 form
        $thesis = $student->theses()->latest()->first();
        if ($thesis && $thesis->pts1Form) {
            $pts1 = $thesis->pts1Form;
            if ($pts1->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form is currently in progress.');
            }
            if ($pts1->status === 'accepted') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form has already been approved.');
            }
        }

        return view('student.pts1.create', compact('user', 'student', 'thesis'));
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

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="publication_list_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['S.No', 'Title of Paper', 'Journal/Conference Name', 'Volume/Issue', 'Publication Date', 'Indexing (SCI/Scopus)', 'Status (Published/Accepted)']);
            fputcsv($file, ['1', 'Deep Learning for Academic Workflows', 'IEEE Transactions on Education', 'Vol. 12, No. 3', '2025-06-15', 'SCI', 'Published']);
            fputcsv($file, ['2', 'Automated PhD Progress Tracking', 'ACM SIGCSE Symposium', 'Proc. PP. 102-108', '2026-01-20', 'Scopus', 'Accepted']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        // Standard default php.ini file upload limit: 2 MB (2048 KB)
        $validated = $request->validate([
            'date_confirmation' => 'required|date',
            'seminar_date' => 'required|date',
            'seminar_time' => 'required|string|max:100',
            'seminar_venue' => 'required|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            
            'publication_norm_fulfillment' => 'required|boolean',
            'special_approval_publication' => 'required_if:publication_norm_fulfillment,0|nullable|boolean',
            'publication_approval_doc' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'min_time_req_fulfilled' => 'required|boolean',
            'special_approval_min_time' => 'required_if:min_time_req_fulfilled,0|nullable|boolean',
            'min_time_approval_doc' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'draft_synopsis_report' => 'required|file|mimes:pdf,docx|max:2048',
            'publication_list' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        // Guard validation: If norm/min-time is false and special approval is false, reject
        if (!$request->boolean('publication_norm_fulfillment') && !$request->boolean('special_approval_publication')) {
            return back()->withInput()->withErrors(['special_approval_publication' => 'Special approval is required when publication norm criteria is not fulfilled.']);
        }

        if (!$request->boolean('min_time_req_fulfilled') && !$request->boolean('special_approval_min_time')) {
            return back()->withInput()->withErrors(['special_approval_min_time' => 'Special approval is required when minimum time requirement criteria is not fulfilled.']);
        }

        // Update student confirmation date
        $student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Retrieve or instantiate Thesis for Student
        $thesis = $student->theses()->latest()->first();
        if (!$thesis) {
            $thesis = Thesis::create([
                'student_id' => $student->id,
                'title' => 'Ph.D. Thesis Research',
                'current_status' => 'In Progress',
            ]);
        }

        // Handle private local file uploads (storage/app/private/pts1_documents/)
        $pubAppDocPath = null;
        if ($request->hasFile('publication_approval_doc')) {
            $pubAppDocPath = $request->file('publication_approval_doc')->store('private/pts1_documents', 'local');
        }

        $minTimeAppDocPath = null;
        if ($request->hasFile('min_time_approval_doc')) {
            $minTimeAppDocPath = $request->file('min_time_approval_doc')->store('private/pts1_documents', 'local');
        }

        $synopsisPath = $request->file('draft_synopsis_report')->store('private/pts1_documents', 'local');
        $pubListPath = $request->file('publication_list')->store('private/pts1_documents', 'local');

        // Committee Co-Supervisors & PSPC IDs
        $coSupervisors = $thesis->coSupervisors()->pluck('users.id')->all();
        $pspcMembers = $thesis->pspcMembers()->pluck('users.id')->all();

        // Create or Update PTS-1 Form
        Pts1Form::updateOrCreate(
            ['thesis_id' => $thesis->id],
            [
                'seminar_date' => $validated['seminar_date'],
                'seminar_time' => $validated['seminar_time'],
                'seminar_venue' => $validated['seminar_venue'],
                'meeting_link' => $validated['meeting_link'],
                'publication_norm_fulfillment' => $request->boolean('publication_norm_fulfillment'),
                'special_approval_publication' => $request->boolean('special_approval_publication'),
                'publication_approval_doc_path' => $pubAppDocPath,
                'min_time_req_fulfilled' => $request->boolean('min_time_req_fulfilled'),
                'special_approval_min_time' => $request->boolean('special_approval_min_time'),
                'min_time_approval_doc_path' => $minTimeAppDocPath,
                'draft_synopsis_report_doc_path' => $synopsisPath,
                'publication_list_doc_path' => $pubListPath,
                'work_status' => 'adequate',
                'main_supervisor_student_comment' => 'N/A',
                'main_supervisor_confidential_remark' => 'N/A',
                'co_supervisor_1_confidential_remark' => 'N/A',
                'co_supervisor_2_confidential_remark' => 'N/A',
                'co_supervisor_3_confidential_remark' => 'N/A',
                'pspc_member_1_confidential_remark' => 'N/A',
                'pspc_member_2_confidential_remark' => 'N/A',
                'pspc_member_3_confidential_remark' => 'N/A',
                'dpgc_confidential_remark' => 'N/A',
                'hod_confidential_remark' => 'N/A',
                'section_officer_confidential_remark' => 'N/A',
                'doaa_confidential_remark' => 'N/A',
                'current_stage' => 'main_supervisor',
                'status' => 'in_progress',
                'co_supervisor_1_id' => $coSupervisors[0] ?? null,
                'co_supervisor_2_id' => $coSupervisors[1] ?? null,
                'co_supervisor_3_id' => $coSupervisors[2] ?? null,
                'pspc_member_1_id' => $pspcMembers[0] ?? null,
                'pspc_member_2_id' => $pspcMembers[1] ?? null,
                'pspc_member_3_id' => $pspcMembers[2] ?? null,
            ]
        );

        return redirect()->route('student.dashboard')->with('success', 'PTS-1 form submitted successfully and forwarded to your Main Supervisor for review!');
    }
}
