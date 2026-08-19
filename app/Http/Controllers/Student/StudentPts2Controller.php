<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Pts2Form;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPts2Controller extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('warning', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'No active registered thesis found.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'accepted')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted') {
            return redirect()->route('student.dashboard')->with('warning', 'PTS-2 Synopsis Form is locked until your PTS-1 Form is fully approved by DOAA.');
        }

        // Must be within the 15-day Open Seminar deadline (or approved extension deadline)
        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('warning', 'The PTS-2 submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'the deadline') . '. Please apply for a PTS-2 extension if eligible.');
        }

        $pts2 = $thesis->pts2Form;

        return view('student.pts2.create', compact('student', 'thesis', 'pts2'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form', 'pts2Extension'])->firstOrFail();

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted') {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized: PTS-1 is not approved.');
        }

        // Must be within the 15-day Open Seminar deadline (or approved extension deadline)
        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('error', 'Cannot submit PTS-2: the submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'N/A') . '.');
        }

        $pts2 = $thesis->pts2Form;
        $fileRequired = $pts2 && $pts2->synopsis_report_doc_path ? 'nullable' : 'required';

        $validated = $request->validate([
            'thesis_title' => 'required|string|max:1000',
            'remarks' => 'nullable|string',
            'synopsis_report_doc' => "{$fileRequired}|file|mimes:pdf,doc,docx|max:2048",
        ]);

        // Update active thesis title
        $thesis->update(['title' => $validated['thesis_title']]);

        $filePath = $pts2?->synopsis_report_doc_path;
        if ($request->hasFile('synopsis_report_doc')) {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }
            $filePath = $request->file('synopsis_report_doc')->store('private/pts2_documents', 'local');
        }

        $pts1 = $thesis->pts1Form;

        Pts2Form::updateOrCreate(
            ['thesis_id' => $thesis->id],
            [
                'thesis_title' => $validated['thesis_title'],
                'remarks' => $validated['remarks'] ?? 'N/A',
                'synopsis_report_doc_path' => $filePath,
                'current_stage' => 'main_supervisor',
                'status' => 'in_progress',
                'reverted_by_role' => null,
                'co_supervisor_1_id' => $pts1->co_supervisor_1_id,
                'co_supervisor_2_id' => $pts1->co_supervisor_2_id,
                'co_supervisor_3_id' => $pts1->co_supervisor_3_id,
                'pspc_member_1_id' => $pts1->pspc_member_1_id,
                'pspc_member_2_id' => $pts1->pspc_member_2_id,
                'pspc_member_3_id' => $pts1->pspc_member_3_id,
                'main_supervisor_confidential_remark' => null,
                'co_supervisor_1_confidential_remark' => null,
                'co_supervisor_2_confidential_remark' => null,
                'co_supervisor_3_confidential_remark' => null,
                'pspc_member_1_confidential_remark' => null,
                'pspc_member_2_confidential_remark' => null,
                'pspc_member_3_confidential_remark' => null,
                'dpgc_confidential_remark' => null,
                'hod_confidential_remark' => null,
                'section_officer_confidential_remark' => null,
                'doaa_confidential_remark' => null,
                'main_supervisor_recommendation' => false,
                'co_supervisor_1_recommendation' => false,
                'co_supervisor_2_recommendation' => false,
                'co_supervisor_3_recommendation' => false,
                'pspc_member_1_recommendation' => false,
                'pspc_member_2_recommendation' => false,
                'pspc_member_3_recommendation' => false,
                'dpgc_recommendation' => false,
                'hod_recommendation' => false,
                'section_officer_recommendation' => false,
                'doaa_approval' => false,
            ]
        );

        return redirect()->route('student.dashboard')->with('success', 'PTS-2 Synopsis Form submitted successfully and forwarded to your Main Supervisor.');
    }
}
