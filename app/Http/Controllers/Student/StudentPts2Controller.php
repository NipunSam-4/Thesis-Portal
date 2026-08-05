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
            return redirect()->route('student.dashboard')->with('warning', 'Scholar profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'No registered thesis found.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'accepted')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted') {
            return redirect()->route('student.dashboard')->with('warning', 'PTS-2 Synopsis Form is locked until your PTS-1 Form is fully approved by DOAA.');
        }

        $pts2 = $thesis->pts2Form;

        return view('student.pts2.create', compact('student', 'thesis', 'pts2'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Scholar profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->with(['pts1Form', 'pts2Form'])->firstOrFail();

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'accepted') {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized: PTS-1 is not approved.');
        }

        $pts2 = $thesis->pts2Form;
        $fileRequired = $pts2 && $pts2->synopsis_report_doc_path ? 'nullable' : 'required';

        $validated = $request->validate([
            'synopsis_title' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'synopsis_report_doc' => "{$fileRequired}|file|mimes:pdf,doc,docx|max:2048",
        ]);

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
                'synopsis_title' => $validated['synopsis_title'],
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
                'main_supervisor_comment' => 'N/A',
                'co_supervisor_1_comment' => 'N/A',
                'co_supervisor_2_comment' => 'N/A',
                'co_supervisor_3_comment' => 'N/A',
                'pspc_member_1_comment' => 'N/A',
                'pspc_member_2_comment' => 'N/A',
                'pspc_member_3_comment' => 'N/A',
                'dpgc_comment' => 'N/A',
                'hod_comment' => 'N/A',
                'section_officer_comment' => 'N/A',
                'doaa_comment' => 'N/A',
                'main_supervisor_endorsement' => false,
                'co_supervisor_1_endorsement' => false,
                'co_supervisor_2_endorsement' => false,
                'co_supervisor_3_endorsement' => false,
                'pspc_member_1_endorsement' => false,
                'pspc_member_2_endorsement' => false,
                'pspc_member_3_endorsement' => false,
                'dpgc_endorsement' => false,
                'hod_endorsement' => false,
                'section_officer_endorsement' => false,
                'doaa_endorsement' => false,
            ]
        );

        return redirect()->route('student.dashboard')->with('success', 'PTS-2 Synopsis Form submitted successfully and forwarded to your Main Supervisor.');
    }
}
