<?php

namespace App\Http\Controllers;

use App\Models\Pts4Extension;
use App\Models\Student;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pts4ExtensionController extends Controller
{
    // Show form for student to apply for PTS-4 Extension.
    public function create()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403, 'Only students can apply for PTS-4 Extension.');
        }

        $student = $user->student;
        if (!$student) {
            abort(404, 'Student profile not found.');
        }

        $thesis = $student->activeThesis;
        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'You must have an active thesis registered to apply for PTS-4 extension.');
        }

        // Must have an APPROVED PTS-1 Form
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'You must have a fully approved PTS-1 Form to apply for PTS-4 extension.');
        }

        // Must have an APPROVED PTS-2 Form
        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'You must have a fully approved PTS-2 Form to apply for PTS-4 extension.');
        }

        // Extension application is allowed up to 60 days after open seminar
        if (!$thesis->canApplyForPts4Extension()) {
            $maxExtDate = $thesis->getMaxPts4ExtensionDate();
            return redirect()->route('student.dashboard')->with('warning', 'The window to apply for PTS-4 extension has expired (maximum 60 days from Open Seminar passed on ' . ($maxExtDate ? $maxExtDate->format('d-M-Y') : 'the deadline') . ').');
        }

        $existingExtension = Pts4Extension::where('thesis_id', $thesis->id)->latest()->first();

        if ($existingExtension && $existingExtension->status === 'in_progress') {
            return redirect()->route('student.dashboard')->with('warning', 'Your PTS-4 extension application is currently under review.');
        }

        $isReverted = $existingExtension && $existingExtension->status === 'reverted';
        $pts4Extension = $isReverted ? $existingExtension : null;

        $seminarDate = $thesis->getOpenSeminarDate();
        $minExtensionDate = $thesis->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis->getMaxPts4ExtensionDate();

        return view('student.pts4_extension.create', compact('user', 'student', 'thesis', 'pts4Extension', 'isReverted', 'seminarDate', 'minExtensionDate', 'maxExtensionDate'));
    }

    // Store or resubmit student PTS-4 extension application.
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403, 'Only students can apply for PTS-4 Extension.');
        }

        $student = $user->student;
        $thesis = $student->activeThesis;

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('error', 'Active thesis not found.');
        }

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'You must have a fully approved PTS-1 Form to apply for PTS-4 extension.');
        }

        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'You must have a fully approved PTS-2 Form to apply for PTS-4 extension.');
        }

        if (!$thesis->canApplyForPts4Extension()) {
            return redirect()->route('student.dashboard')->with('error', 'The window to apply for PTS-4 extension has expired (maximum 60 days from Open Seminar).');
        }

        $minExtensionDate = $thesis->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis->getMaxPts4ExtensionDate();

        $request->validate([
            'reason_for_extension' => 'required|string|max:2000',
            'extended_until_date' => [
                'required',
                'date',
                'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+60 days'),
            ],
        ], [
            'extended_until_date.after_or_equal' => 'Extension date must be on or after ' . ($minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A') . '.',
            'extended_until_date.before_or_equal' => 'Extension date cannot exceed 60 days from Open Seminar (' . ($maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'N/A') . ').',
        ]);

        $existingExtension = Pts4Extension::where('thesis_id', $thesis->id)->latest()->first();

        if ($existingExtension && $existingExtension->status === 'in_progress') {
            return redirect()->route('student.dashboard')->with('warning', 'Your PTS-4 extension application is currently under review.');
        }

        if ($existingExtension && $existingExtension->status === 'reverted') {
            $existingExtension->update([
                'reason_for_extension' => $request->reason_for_extension,
                'extended_until_date' => $request->extended_until_date,
                'status' => 'in_progress',
                'current_stage' => 'main_supervisor',
                'reverted_by_id' => null,
                'reverted_by_role' => null,
                'reversion_comment' => null,
            ]);
        } else {
            Pts4Extension::create([
                'thesis_id' => $thesis->id,
                'reason_for_extension' => $request->reason_for_extension,
                'extended_until_date' => $request->extended_until_date,
                'current_stage' => 'main_supervisor',
                'status' => 'in_progress',
            ]);
        }

        $prefix = $student->isPhd() ? 'PTS' : 'MSRTS';
        return redirect()->route('student.dashboard')->with('success', "{$prefix}-4 Extension application submitted successfully to Main Supervisor for endorsement.");
    }

    // Read-only view of a PTS-4 Extension application.
    public function show(Pts4Extension $pts4Extension)
    {
        $this->authorize('view', $pts4Extension);

        $user = Auth::user();
        $extension = $pts4Extension->loadMissing(['thesis.student.user', 'thesis.student.department']);
        $thesis = $extension->thesis;
        $student = $thesis->student;

        $seminarDate = $thesis->getOpenSeminarDate();
        return view('pts4_extension.show', compact('user', 'student', 'thesis', 'extension', 'seminarDate'));
    }

    // Review & Endorsement page for authorities.
    public function review(Pts4Extension $pts4Extension)
    {
        $this->authorize('evaluate', $pts4Extension);

        $user = Auth::user();
        $extension = $pts4Extension->loadMissing(['thesis.student.user', 'thesis.student.department']);
        $thesis = $extension->thesis;
        $student = $thesis->student;

        $userRole = $this->determineUserRole($user, $extension);
        $seminarDate = $thesis->getOpenSeminarDate();
        $minExtensionDate = $thesis->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis->getMaxPts4ExtensionDate();

        return view('pts4_extension.review', compact('user', 'student', 'thesis', 'extension', 'userRole', 'seminarDate', 'minExtensionDate', 'maxExtensionDate'));
    }

    // Submit Review (Endorse, Revert, Reject, Approve).
    public function submitReview(Request $request, Pts4Extension $pts4Extension)
    {
        $this->authorize('evaluate', $pts4Extension);

        $user = Auth::user();
        $extension = $pts4Extension;
        $thesis = $extension->thesis;
        $student = $thesis->student;

        $userRole = $this->determineUserRole($user, $extension);

        $action = $request->input('action');
        $minExtensionDate = $thesis->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis->getMaxPts4ExtensionDate();

        $prefix = $student->isPhd() ? 'PTS' : 'MSRTS';

        if ($action === 'endorse') {
            $dataToUpdate = [
                "{$userRole}_id" => $user->id,
                "{$userRole}_comments" => $request->input('comments'),
                "{$userRole}_signed_at" => now(),
            ];

            if ($userRole === 'doaa') {
                $request->validate([
                    'approved_extended_until_date' => [
                        'required',
                        'date',
                        'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                        'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+60 days'),
                    ],
                ]);
                $dataToUpdate['approved_extended_until_date'] = $request->approved_extended_until_date;
                $dataToUpdate['status'] = 'approved';
                $dataToUpdate['current_stage'] = 'completed';
            } else {
                $nextStage = match ($userRole) {
                    'main_supervisor' => 'dpgc',
                    'dpgc'            => 'hod',
                    'hod'             => 'academic_office',
                    'academic_office' => 'doaa',
                    default           => 'completed',
                };
                $dataToUpdate['current_stage'] = $nextStage;
            }

            $extension->update($dataToUpdate);

            $msg = $userRole === 'doaa'
                ? "{$prefix}-4 Extension approved successfully."
                : "{$prefix}-4 Extension endorsed and forwarded to the next authority.";

            return redirect()->route($this->getDashboardRouteForUser($user))->with('success', $msg);
        }

        if ($action === 'revert') {
            $request->validate(['reversion_comment' => 'required|string|max:2000']);

            $extension->update([
                'status' => 'reverted',
                'reverted_by_id' => $user->id,
                'reverted_by_role' => $userRole,
                'reversion_comment' => $request->input('reversion_comment'),
            ]);

            return redirect()->route($this->getDashboardRouteForUser($user))->with('warning', "{$prefix}-4 Extension application reverted to the student.");
        }

        if ($action === 'reject') {
            $request->validate(['rejection_comment' => 'required|string|max:2000']);

            $extension->update([
                'status' => 'rejected',
                'current_stage' => 'rejected',
                'rejection_comment' => $request->input('rejection_comment'),
            ]);

            return redirect()->route($this->getDashboardRouteForUser($user))->with('error', "{$prefix}-4 Extension application rejected.");
        }

        abort(400, 'Invalid review action.');
    }

    private function canUserAccessExtension($user, Pts4Extension $extension, Student $student): bool
    {
        if ($user->isStudent() && $student->user_id === $user->id) return true;
        if ($student->isSupervisor($user)) return true;
        if ($student->isPspcMember($user)) return true;
        if ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa()) return true;
        if ($user->isActingApprovalAuthority()) return true;
        if (($user->isDpgc() || $user->isHod()) && $user->deptAuthorityProfile?->department_id === $student->department_id) return true;

        return false;
    }

    private function determineUserRole($user, Pts4Extension $extension): ?string
    {
        $student = $extension->thesis->student;

        if ($extension->current_stage === 'main_supervisor' && $student->isMainSupervisor($user)) {
            return 'main_supervisor';
        }
        if ($extension->current_stage === 'dpgc' && $user->isDpgc() && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 'dpgc';
        }
        if ($extension->current_stage === 'hod' && $user->isHod() && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 'hod';
        }
        if ($extension->current_stage === 'academic_office' && $user->isAcademicOffice()) {
            return 'academic_office';
        }
        if ($extension->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($extension->acting_doaa_email === $user->email || $extension->vested_doaa_email === $user->email)))) {
            return 'doaa';
        }

        return null;
    }

    private function getDashboardRouteForUser($user): string
    {
        if ($user->isDoaa() || $user->isAcademicOffice()) return 'global_authorities.dashboard';
        if ($user->isDpgc() || $user->isHod()) return 'dept_authorities.dashboard';
        if ($user->isActingApprovalAuthority()) return 'acting_approval_authority.dashboard';
        return 'faculty.dashboard';
    }
}
