<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Pts2Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacultyPts2Controller extends Controller
{
    public function edit(Pts2Form $pts2)
    {
        $user = auth()->user();

        // Ensure user is Main Supervisor for this thesis
        $isMain = $pts2->thesis->supervisors()
            ->where('users.id', $user->id)
            ->where('supervisor_type', 'main')
            ->exists();

        if (!$isMain) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access.');
        }

        $scholar = $pts2->thesis->student;

        return view('faculty.pts2.review', compact('pts2', 'scholar'));
    }

    public function update(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();

        $isMain = $pts2->thesis->supervisors()
            ->where('users.id', $user->id)
            ->where('supervisor_type', 'main')
            ->exists();

        if (!$isMain) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,revert',
            'synopsis_title' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'synopsis_report_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'comment' => 'nullable|string',
        ]);

        // File replacement by supervisor if uploaded
        $filePath = $pts2->synopsis_report_doc_path;
        if ($request->hasFile('synopsis_report_doc')) {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }
            $filePath = $request->file('synopsis_report_doc')->store('pts2_documents', 'local');
        }

        $pts2->update([
            'synopsis_title' => $validated['synopsis_title'],
            'remarks' => $validated['remarks'] ?? null,
            'synopsis_report_doc_path' => $filePath,
        ]);

        if ($validated['action'] === 'revert') {
            $pts2->update([
                'status' => 'reverted',
                'reverted_by_role' => 'main_supervisor',
                'current_stage' => 'rejected',
                'main_supervisor_comment' => $validated['comment'] ?? 'Reverted by Main Supervisor.',
            ]);

            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-2 Synopsis Form reverted back to scholar.');
        }

        // Action: Approve
        $nextStage = 'dpgc';
        if ($pts2->co_supervisor_1_id || $pts2->co_supervisor_2_id || $pts2->co_supervisor_3_id) {
            $nextStage = 'co_supervisors';
        } elseif ($pts2->pspc_member_1_id || $pts2->pspc_member_2_id || $pts2->pspc_member_3_id) {
            $nextStage = 'pspc_members';
        }

        $pts2->update([
            'main_supervisor_endorsement' => true,
            'main_supervisor_comment' => $validated['comment'] ?: 'N/A',
            'current_stage' => $nextStage,
            'status' => 'in_progress',
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-2 Synopsis Form approved and forwarded to next stage.');
    }
}
