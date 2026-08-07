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
        $scholarUser = $student->user;

        return view('faculty.pts1.review', compact('pts1', 'thesis', 'student', 'scholarUser'));
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
            'publication_list' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',

            'work_status' => 'required|in:adequate,inadequate',
            'additional_comments' => 'required|string',
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

        // Evaluate Work Status: Option (b) INADEQUATE -> Revert to Student
        if ($validated['work_status'] === 'inadequate') {
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
                'work_status' => 'inadequate',
                'additional_comments' => $validated['additional_comments'],
                'main_supervisor_comment' => $validated['additional_comments'],
                'status' => 'reverted',
                'reverted_by_role' => 'main_supervisor',
                'current_stage' => 'rejected',
            ]);

            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-1 form evaluated as INADEQUATE and reverted to scholar for modifications.');
        }

        // Evaluate Work Status: Option (a) ADEQUATE -> Endorse & Advance
        $coSupervisorsCount = $thesis->coSupervisors()->count();
        $pspcMembersCount = $thesis->pspcMembers()->count();

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
            'work_status' => 'adequate',
            'additional_comments' => $validated['additional_comments'],
            'main_supervisor_comment' => $validated['additional_comments'],
            'main_supervisor_endorsement' => true,
            'current_stage' => $nextStage,
            'status' => 'in_progress',
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-1 form endorsed successfully and forwarded to next stage.');
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
            ->exists() ||
            $pts1->co_supervisor_1_id === $user->id ||
            $pts1->co_supervisor_2_id === $user->id ||
            $pts1->co_supervisor_3_id === $user->id;

        if (!$isCoSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to Co-Supervisor PTS-1 review.');
        }

        $student = $thesis->student;
        $scholarUser = $student->user;
        $mainSupervisor = $thesis->mainSupervisor;

        return view('faculty.pts1.co_review', compact('pts1', 'thesis', 'student', 'scholarUser', 'mainSupervisor'));
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
        $roleKey = 'co_supervisor_1';
        if ($pts1->co_supervisor_2_id === $user->id) {
            $roleKey = 'co_supervisor_2';
        } elseif ($pts1->co_supervisor_3_id === $user->id) {
            $roleKey = 'co_supervisor_3';
        }

        if ($validated['action'] === 'revert') {
            $pts1->update([
                "{$roleKey}_comment" => $validated['comment'],
                'reverted_by_role' => $roleKey,
                'status' => 'reverted',
                'current_stage' => 'rejected',
            ]);

            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-1 Form reverted back to scholar.');
        }

        $pts1->update([
            "{$roleKey}_endorsement" => true,
            "{$roleKey}_comment" => $validated['comment'] ?: 'N/A',
        ]);

        $co1Done = !$pts1->co_supervisor_1_id || $pts1->co_supervisor_1_endorsement;
        $co2Done = !$pts1->co_supervisor_2_id || $pts1->co_supervisor_2_endorsement;
        $co3Done = !$pts1->co_supervisor_3_id || $pts1->co_supervisor_3_endorsement;

        if ($co1Done && $co2Done && $co3Done) {
            $nextStage = ($pts1->pspc_member_1_id || $pts1->pspc_member_2_id || $pts1->pspc_member_3_id) ? 'pspc_members' : 'dpgc';
            $pts1->update(['current_stage' => $nextStage]);
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-1 Form endorsed successfully.');
    }
}
