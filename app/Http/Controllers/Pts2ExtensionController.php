<?php

namespace App\Http\Controllers;

use App\Models\Pts2Extension;
use App\Models\Student;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pts2ExtensionController extends Controller
{
    // Show form for student to apply for PTS-2 Extension.
    public function create()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403, 'Only students can apply for PTS-2 Extension.');
        }

        $student = $user->student;
        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        $thesis = $student->activeThesis;
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'You must have an active thesis registered to apply for PTS-2 extension.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'approved')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'You must have a fully approved PTS-1 Form to apply for PTS-2 extension.');
        }

        // Extension application is allowed up to 30 days after open seminar
        if (!$thesis->canApplyForPts2Extension()) {
            $maxExtDate = $thesis->getMaxExtensionDate();
            return redirect()->route('student.dashboard')->with('warning', 'The window to apply for PTS-2 extension has expired (maximum 30 days from Open Seminar passed on ' . ($maxExtDate ? $maxExtDate->format('d-M-Y') : 'the deadline') . ').');
        }

        $existingExtension = Pts2Extension::where('thesis_id', $thesis->id)->latest()->first();

        if ($existingExtension && $existingExtension->status === 'in_progress') {
            return redirect()->route('student.dashboard')->with('warning', 'Your PTS-2 extension application is currently under review.');
        }

        $isReverted = $existingExtension && $existingExtension->status === 'reverted';
        $pts2Extension = $isReverted ? $existingExtension : null;

        $seminarDate = $thesis->getOpenSeminarDate();
        $minExtensionDate = $thesis->getMinExtensionDate();
        $maxExtensionDate = $thesis->getMaxExtensionDate();

        return view('student.pts2_extension.create', compact('user', 'student', 'thesis', 'pts2Extension', 'isReverted', 'seminarDate', 'minExtensionDate', 'maxExtensionDate'));
    }

    // Store or resubmit student PTS-2 extension application.
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403, 'Only students can apply for PTS-2 Extension.');
        }

        $student = $user->student;
        $thesis = $student->activeThesis;

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('error', 'Active thesis not found.');
        }

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'You must have a fully approved PTS-1 Form to apply for PTS-2 extension.');
        }

        if (!$thesis->canApplyForPts2Extension()) {
            return redirect()->route('student.dashboard')->with('error', 'The window to apply for PTS-2 extension has expired (maximum 30 days from Open Seminar).');
        }

        $minExtensionDate = $thesis->getMinExtensionDate();
        $maxExtensionDate = $thesis->getMaxExtensionDate();

        $request->validate([
            'reason_for_extension' => 'required|string|max:5000',
            'extended_until_date' => [
                'required',
                'date',
                'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+30 days'),
            ],
        ], [
            'extended_until_date.after_or_equal' => 'Extension date must be at least 16 days from Open Seminar (' . ($minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A') . ').',
            'extended_until_date.before_or_equal' => 'Extension date cannot exceed 30 days from Open Seminar (' . ($maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'N/A') . ').',
        ]);

        $existingExtension = Pts2Extension::where('thesis_id', $thesis->id)->latest()->first();

        if ($existingExtension && $existingExtension->status === 'in_progress') {
            return redirect()->route('student.dashboard')->with('warning', 'Your PTS-2 extension application is currently under review.');
        }

        Pts2Extension::create([
            'thesis_id' => $thesis->id,
            'reason_for_extension' => $request->reason_for_extension,
            'extended_until_date' => $request->extended_until_date,
            'status' => 'in_progress',
            'current_stage' => 'main_supervisor',
        ]);

        return redirect()->route('student.dashboard')->with('success', 'PTS-2 Extension application submitted successfully.');
    }

    // Show PTS-2 extension view for student or authority.
    public function show($id)
    {
        $extension = Pts2Extension::with(['thesis.student.user', 'thesis.student.department'])->findOrFail($id);
        $user = Auth::user();

        if (!$user->isStudent()) {
            $userRole = $this->determineUserRole($user, $extension);
            if ($userRole && $extension->status === 'in_progress') {
                $viewerRank = Pts2Extension::getRoleRank($userRole);
                $currentStageRank = Pts2Extension::getRoleRank($extension->current_stage);
                if ($viewerRank > $currentStageRank) {
                    return redirect()->back()->with('warning', 'This PTS-2 extension application has not reached your evaluation stage yet.');
                }
            }
        }

        return view('pts2_extension.show', compact('extension', 'user'));
    }

    // Show evaluation portal for authority.
    public function review($id)
    {
        $extension = Pts2Extension::with(['thesis.student.user', 'thesis.student.department'])->findOrFail($id);
        $user = Auth::user();

        // Determine user authority role
        $userRole = $this->determineUserRole($user, $extension);
        if (!$userRole) {
            abort(403, 'You are not authorized to evaluate this extension request.');
        }

        $seminarDate = $extension->thesis?->getOpenSeminarDate();
        $minExtensionDate = $extension->thesis?->getMinExtensionDate();
        $maxExtensionDate = $extension->thesis?->getMaxExtensionDate();

        return view('pts2_extension.review', compact('extension', 'user', 'userRole', 'seminarDate', 'minExtensionDate', 'maxExtensionDate'));
    }

    // Handle evaluation submission by an authority.
    public function submitReview(Request $request, $id)
    {
        $extension = Pts2Extension::findOrFail($id);
        $user = Auth::user();

        $userRole = $this->determineUserRole($user, $extension);
        if (!$userRole) {
            abort(403, 'You are not authorized to evaluate this extension request.');
        }

        if ($extension->current_stage !== $userRole) {
            return redirect()->back()->with('error', 'This extension application is currently not pending at your evaluation stage.');
        }

        // 1. Handle Pop-Up Modal Reversion (All Authorities Including DOAA)
        if ($request->action === 'revert' || $request->filled('reversion_comment')) {
            $request->validate([
                'reversion_comment' => 'required|string|max:3000',
            ]);

            $extension->update([
                'status' => 'reverted',
                'current_stage' => 'reverted',
                'reverted_by_role' => $userRole,
                'reverted_by_id' => $user->id,
                'reversion_comment' => $request->reversion_comment,
            ]);

            return redirect()->route('dashboard')->with('success', 'PTS-2 Extension request reverted back to the student.');
        }

        // 2. Handle Recommendation / Approval Form
        $minExtensionDate = $extension->thesis?->getMinExtensionDate();
        $maxExtensionDate = $extension->thesis?->getMaxExtensionDate();

        if ($userRole === 'section_officer') {
            $request->validate([
                'confidential_remark' => 'required|string|max:3000',
            ]);
            $isRecommended = true;
        } else {
            $approvedDateRules = ['nullable', 'date'];
            if ($userRole === 'doaa' && $request->recommendation == '1') {
                $approvedDateRules = [
                    'required',
                    'date',
                    'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                    'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+30 days'),
                ];
            }

            $request->validate([
                'recommendation' => 'required|in:1,0',
                'confidential_remark' => $request->recommendation === '0' ? 'required|string|max:3000' : 'nullable|string|max:3000',
                'doaa_student_comment' => $userRole === 'doaa' ? 'required|string|max:3000' : 'nullable|string|max:3000',
                'approved_extended_until_date' => $approvedDateRules,
            ], [
                'approved_extended_until_date.after_or_equal' => 'Approved extension date must be at least 16 days from Open Seminar (' . ($minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A') . ').',
                'approved_extended_until_date.before_or_equal' => 'Approved extension date cannot exceed 30 days from Open Seminar (' . ($maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'N/A') . ').',
            ]);
            $isRecommended = $request->recommendation == '1';
        }

        switch ($userRole) {
            case 'main_supervisor':
                $extension->main_supervisor_recommendation = $isRecommended;
                $extension->main_supervisor_confidential_remark = $request->confidential_remark;
                $extension->main_supervisor_submitted_at = now();
                $extension->current_stage = 'dpgc';
                break;

            case 'dpgc':
                $extension->dpgc_recommendation = $isRecommended;
                $extension->dpgc_confidential_remark = $request->confidential_remark;
                $extension->dpgc_submitted_at = now();
                $extension->current_stage = 'hod';
                break;

            case 'hod':
                $extension->hod_recommendation = $isRecommended;
                $extension->hod_confidential_remark = $request->confidential_remark;
                $extension->hod_submitted_at = now();
                $extension->current_stage = 'section_officer';
                break;

            case 'section_officer':
                $extension->section_officer_recommendation = $isRecommended;
                $extension->section_officer_confidential_remark = $request->confidential_remark;
                $extension->section_officer_submitted_at = now();
                $extension->current_stage = 'doaa';
                break;

            case 'doaa':
                $extension->doaa_recommendation = $isRecommended;
                $extension->doaa_confidential_remark = $request->confidential_remark;
                $extension->doaa_student_comment = $request->doaa_student_comment;
                $extension->doaa_submitted_at = now();
                if ($isRecommended) {
                    $extension->approved_extended_until_date = $request->approved_extended_until_date ?? $extension->extended_until_date;
                    $extension->status = 'approved';
                } else {
                    $extension->approved_extended_until_date = null;
                    $extension->status = 'rejected';
                }
                $extension->current_stage = 'completed';
                break;
        }

        $extension->save();

        return redirect()->route('dashboard')->with('success', 'PTS-2 Extension Application submitted successfully.');
    }

    // Helper to determine logged-in user's role in relation to the extension form.
    private function determineUserRole($user, Pts2Extension $extension): ?string
    {
        $student = $extension->thesis?->student;

        if ($student && $student->isMainSupervisor($user)) {
            return 'main_supervisor';
        }

        if ($user->isDpgc()) {
            return 'dpgc';
        }

        if ($user->isHod()) {
            return 'hod';
        }

        if ($user->isSectionOfficer()) {
            return 'section_officer';
        }

        if ($user->isDoaa()) {
            return 'doaa';
        }

        return null;
    }
}
