<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Pts1Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacultyPts1Controller extends Controller
{
    /**
     * Show the Main Supervisor review & edit form for a PTS-1 submission.
     */
    public function edit(Pts1Form $pts1)
    {
        $user = auth()->user();

        // Check if logged-in user is Main Supervisor for this thesis
        $thesis = $pts1->thesis;
        $isMainSupervisor = $thesis->supervisors()
            ->where('users.id', $user->id)
            ->wherePivot('supervisor_type', 'main')
            ->exists();

        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts1.review', compact('pts1', 'thesis', 'student', 'studentUser'));
    }

    /**
     * Process Main Supervisor review submission (Edits, Evaluation & Endorsement/Reversion).
     */
    public function update(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;

        $isMainSupervisor = $thesis->supervisors()
            ->where('users.id', $user->id)
            ->wherePivot('supervisor_type', 'main')
            ->exists();

        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

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

            'draft_synopsis_report' => 'nullable|file|mimes:pdf,docx|max:2048',
            'publication_list' => 'nullable|file|mimes:xlsx,xls|max:2048',

            'work_status' => 'required|in:adequate,inadequate',
            'main_supervisor_student_comment' => 'required|string',
            'main_supervisor_confidential_remark' => $request->input('work_status') === 'inadequate' ? 'required|string' : 'nullable|string',
        ]);

        // Optional File Replacements by Main Supervisor
        $pubAppPath = $pts1->publication_approval_doc_path;
        if ($request->hasFile('publication_approval_doc')) {
            $pubAppPath = $request->file('publication_approval_doc')->store('private/pts1_documents', 'local');
        }

        $minTimeAppPath = $pts1->min_time_approval_doc_path;
        if ($request->hasFile('min_time_approval_doc')) {
            $minTimeAppPath = $request->file('min_time_approval_doc')->store('private/pts1_documents', 'local');
        }

        $synopsisPath = $pts1->draft_synopsis_report_doc_path;
        if ($request->hasFile('draft_synopsis_report')) {
            $synopsisPath = $request->file('draft_synopsis_report')->store('private/pts1_documents', 'local');
        }

        $pubListPath = $pts1->publication_list_doc_path;
        if ($request->hasFile('publication_list')) {
            $pubListPath = $request->file('publication_list')->store('private/pts1_documents', 'local');
        }

        // Update Student confirmation date
        $thesis->student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Determine Next Stage for Forwarding
        $coSupervisorsCount = $thesis->student ? $thesis->student->coSupervisors()->count() : 0;
        $pspcMembersCount = $thesis->student ? $thesis->student->pspcMembers()->count() : 0;

        $nextStage = 'dpgc';
        if ($coSupervisorsCount > 0) {
            $nextStage = 'co_supervisors';
        } elseif ($pspcMembersCount > 0) {
            $nextStage = 'pspc_members';
        }

        $pts1->update([
            'seminar_date' => $validated['seminar_date'],
            'seminar_time' => $validated['seminar_time'],
            'seminar_venue' => $validated['seminar_venue'],
            'meeting_link' => $validated['meeting_link'],
            'publication_norm_fulfillment' => $request->boolean('publication_norm_fulfillment'),
            'special_approval_publication' => $request->boolean('special_approval_publication'),
            'publication_approval_doc_path' => $pubAppPath,
            'min_time_req_fulfilled' => $request->boolean('min_time_req_fulfilled'),
            'special_approval_min_time' => $request->boolean('special_approval_min_time'),
            'min_time_approval_doc_path' => $minTimeAppPath,
            'draft_synopsis_report_doc_path' => $synopsisPath,
            'publication_list_doc_path' => $pubListPath,
            'work_status' => $validated['work_status'],
            'main_supervisor_student_comment' => $validated['main_supervisor_student_comment'],
            'main_supervisor_confidential_remark' => $validated['main_supervisor_confidential_remark'] ?: 'N/A',
            'main_supervisor_recommendation' => ($validated['work_status'] === 'adequate'),
            'current_stage' => $nextStage,
            'status' => 'in_progress',
            'main_supervisor_submitted_at' => now(),
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-1 form submitted successfully and forwarded to next stage.');
    }

    /**
     * Show the Co-Supervisor review view for a PTS-1 submission.
     */
    public function coEdit(Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;

        $isCoSupervisor = $thesis->supervisors()
            ->where('users.id', $user->id)
            ->wherePivot('supervisor_type', 'co')
            ->exists();

        if (!$isCoSupervisor) {
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts1->$col === $user->id) {
                    $isCoSupervisor = true;
                    break;
                }
            }
        }

        if (!$isCoSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to Co-Supervisor PTS-1 review.');
        }

        $student = $thesis->student;
        $studentUser = $student->user;
        $mainSupervisor = $thesis->mainSupervisor;

        return view('faculty.pts1.co_review', compact('pts1', 'thesis', 'student', 'studentUser', 'mainSupervisor'));
    }

    /**
     * Process Co-Supervisor evaluation & decision (Approve/Revert with comment).
     */
    public function coUpdate(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;

        $validated = $request->validate([
            'action' => 'required|in:approve,revert',
            'comment' => 'required_if:action,revert|nullable|string',
        ]);

        // Determine Co-Supervisor role slot
        $roleKey = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts1->$col === $user->id) {
                $roleKey = "co_supervisor_{$i}";
                break;
            }
        }

        if (!$roleKey) {
            return redirect()->route('faculty.dashboard')->with('error', 'You are not assigned as a Co-Supervisor.');
        }

        if ($validated['action'] === 'revert') {
            $pts1->update([
                "{$roleKey}_confidential_remark" => $validated['comment'],
                "{$roleKey}_reversion_comment" => $validated['comment'],
                'reverted_by_role' => $roleKey,
                'status' => 'reverted',
                'current_stage' => 'rejected',
            ]);

            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-1 Form reverted back to student.');
        }

        $pts1->update([
            "{$roleKey}_recommendation" => true,
            "{$roleKey}_confidential_remark" => $validated['comment'] ?: 'Recommended',
        ]);

        $allCoDone = true;
        for ($i = 1; $i <= 10; $i++) {
            $idCol = "co_supervisor_{$i}_id";
            $remCol = "co_supervisor_{$i}_confidential_remark";
            if ($pts1->$idCol && (is_null($pts1->$remCol))) {
                $allCoDone = false;
                break;
            }
        }

        if ($allCoDone) {
            $hasPspc = false;
            for ($i = 1; $i <= 10; $i++) {
                $col = "pspc_member_{$i}_id";
                if ($pts1->$col) {
                    $hasPspc = true;
                    break;
                }
            }
            $nextStage = $hasPspc ? 'pspc_members' : 'dpgc';
            $pts1->update([
                'current_stage' => $nextStage,
                'co_supervisors_submitted_at' => now(),
            ]);
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-1 Form endorsed successfully.');
    }
}
