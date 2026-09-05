<?php

namespace App\Http\Controllers;

use App\Models\Pts4Extension;
use App\Models\Student;
use App\Models\Thesis;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pts4ExtensionController extends Controller
{
    use AuthorizesRequests;
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
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'You must have an active thesis registered to apply for PTS-4 extension.');
        }

        $thesis->loadMissing(['pts1Form', 'pts2Form']);

        // Must have an APPROVED PTS-1 Form
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'You must have a fully approved PTS-1 Form to apply for PTS-4 extension.');
        }

        // Must have an APPROVED PTS-2 Form
        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'You must have a fully approved PTS-2 Form to apply for PTS-4 extension.');
        }

        // Extension application is allowed up to maximum configured days after open seminar
        if (!$thesis->canApplyForPts4Extension()) {
            $maxExtDate = $thesis->getMaxPts4ExtensionDate();
            return redirect()->route('student.dashboard')->with('warning', 'The window to apply for PTS-4 extension has expired (passed on ' . ($maxExtDate ? $maxExtDate->format('d-M-Y') : 'the deadline') . ').');
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
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('error', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('error', 'Active thesis not found.');
        }

        $thesis->loadMissing(['pts1Form', 'pts2Form']);

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'You must have a fully approved PTS-1 Form to apply for PTS-4 extension.');
        }

        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'You must have a fully approved PTS-2 Form to apply for PTS-4 extension.');
        }

        if (!$thesis->canApplyForPts4Extension()) {
            $maxExtDate = $thesis->getMaxPts4ExtensionDate();
            return redirect()->route('student.dashboard')->with('error', 'The window to apply for PTS-4 extension has expired (passed on ' . ($maxExtDate ? $maxExtDate->format('d-M-Y') : 'the deadline') . ').');
        }

        $minExtensionDate = $thesis->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis->getMaxPts4ExtensionDate();

        $request->validate([
            'reason_for_extension' => 'required|string|max:2000',
            'extended_until_date' => [
                'required',
                'date',
                'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+1 year'),
            ],
        ], [
            'extended_until_date.after_or_equal' => 'Extension date must be on or after ' . ($minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A') . '.',
            'extended_until_date.before_or_equal' => 'Extension date cannot exceed ' . ($maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'the maximum allowed extension limit') . '.',
        ]);

        $existingExtension = Pts4Extension::where('thesis_id', $thesis->id)->latest()->first();
        if ($existingExtension && $existingExtension->status === 'in_progress') {
            return redirect()->route('student.dashboard')->with('warning', 'Your PTS-4 extension application is currently under review.');
        }

        $activeMainSup = $student->active_main_supervisor;
        if (!$activeMainSup) {
            return back()->withInput()->with('error', 'Unable to apply for PTS-4 extension: No active Main Supervisor is assigned to your profile. Please contact the Academic Office.');
        }

        Pts4Extension::create([
            'thesis_id' => $thesis->id,
            'reason_for_extension' => $request->reason_for_extension,
            'extended_until_date' => $request->extended_until_date,
            'status' => 'in_progress',
            'main_supervisor_id' => $activeMainSup->id,
            'vested_doaa_email' => \App\Models\VestedDoaa::getActiveVestedEmail(),
            'current_stage' => 'main_supervisor',
        ]);

        $prefix = $student->isPhd() ? 'PTS' : 'MSRTS';
        return redirect()->route('student.dashboard')->with('success', "{$prefix}-4 Extension application submitted successfully to Main Supervisor for endorsement.");
    }

    // Read-only view of a PTS-4 Extension application.
    public function show(Pts4Extension $pts4Extension)
    {
        $this->authorize('view', $pts4Extension);

        $user = Auth::user();
        $extension = $pts4Extension->loadMissing(['thesis.student.user', 'thesis.student.department', 'approvedBy']);
        $userRole = Thesis::determineExtensionUserRole($user, $extension);

        return view('pts4_extension.show', compact('user', 'extension', 'userRole'));
    }

    // Review & Endorsement page for authorities.
    public function review(Pts4Extension $pts4Extension)
    {
        $this->authorize('review', $pts4Extension);

        $user = Auth::user();
        $extension = $pts4Extension->loadMissing(['thesis.student.user', 'thesis.student.department']);
        $thesis = $extension->thesis;

        $userRole = Thesis::determineExtensionUserRole($user, $extension);
        $seminarDate = $thesis?->getOpenSeminarDate();
        $minExtensionDate = $thesis?->getMinPts4ExtensionDate();
        $maxExtensionDate = $thesis?->getMaxPts4ExtensionDate();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        return view('pts4_extension.review', compact('user', 'extension', 'userRole', 'seminarDate', 'minExtensionDate', 'maxExtensionDate', 'actingDoaaUsers'));
    }

    // Handle evaluation submission (endorsement, verification, approval) by an authority.
    public function endorse(Request $request, Pts4Extension $pts4Extension)
    {
        $this->authorize('review', $pts4Extension);

        $extension = $pts4Extension;
        $user = Auth::user();

        $userRole = Thesis::determineExtensionUserRole($user, $extension);
        if (!$userRole || $extension->current_stage !== $userRole) {
            abort(403, 'You are not authorized to evaluate this extension request at this stage.');
        }

        $minExtensionDate = $extension->thesis?->getMinPts4ExtensionDate();
        $maxExtensionDate = $extension->thesis?->getMaxPts4ExtensionDate();

        $updateData = [];

        if ($userRole === 'academic_office') {
            $request->validate([
                'verified_details' => 'required|accepted',
                'confidential_remark' => 'required|string|max:2000',
                'acting_doaa_email' => 'nullable|email',
            ]);

            $updateData = [
                'academic_office_recommendation' => true,
                'academic_office_confidential_remark' => $request->confidential_remark,
                'academic_office_submitted_at' => now(),
                'academic_office_user_id' => $user->id,
                'acting_doaa_email' => $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null,
                'current_stage' => 'doaa',
            ];
        } else {
            $approvedDateRules = ['nullable', 'date'];
            if ($userRole === 'doaa' && $request->recommendation == '1') {
                $approvedDateRules = [
                    'required',
                    'date',
                    'after_or_equal:' . ($minExtensionDate ? $minExtensionDate->format('Y-m-d') : 'today'),
                    'before_or_equal:' . ($maxExtensionDate ? $maxExtensionDate->format('Y-m-d') : '+1 year'),
                ];
            }

            $request->validate([
                'recommendation' => 'required|in:1,0',
                'confidential_remark' => $request->recommendation === '0' ? 'required|string|max:2000' : 'nullable|string|max:2000',
                'doaa_student_comment' => $userRole === 'doaa' ? 'required|string|max:2000' : 'nullable|string|max:2000',
                'approved_extended_until_date' => $approvedDateRules,
            ], [
                'approved_extended_until_date.after_or_equal' => 'Approved extension date must be on or after ' . ($minExtensionDate ? $minExtensionDate->format('d-M-Y') : 'N/A') . '.',
                'approved_extended_until_date.before_or_equal' => 'Approved extension date cannot exceed ' . ($maxExtensionDate ? $maxExtensionDate->format('d-M-Y') : 'the maximum allowed extension limit') . '.',
            ]);

            $isRecommended = $request->recommendation == '1';

            switch ($userRole) {
                case 'main_supervisor':
                    $updateData = [
                        'main_supervisor_recommendation' => $isRecommended,
                        'main_supervisor_confidential_remark' => $request->confidential_remark,
                        'main_supervisor_submitted_at' => now(),
                        'current_stage' => 'dpgc',
                    ];
                    break;

                case 'dpgc':
                    $updateData = [
                        'dpgc_recommendation' => $isRecommended,
                        'dpgc_confidential_remark' => $request->confidential_remark,
                        'dpgc_submitted_at' => now(),
                        'dpgc_user_id' => $user->id,
                        'current_stage' => 'hod',
                    ];
                    break;

                case 'hod':
                    $updateData = [
                        'hod_recommendation' => $isRecommended,
                        'hod_confidential_remark' => $request->confidential_remark,
                        'hod_submitted_at' => now(),
                        'hod_user_id' => $user->id,
                        'current_stage' => 'academic_office',
                    ];
                    break;

                case 'doaa':
                    $updateData = [
                        'doaa_recommendation' => $isRecommended,
                        'doaa_confidential_remark' => $request->confidential_remark,
                        'doaa_student_comment' => $request->doaa_student_comment,
                        'doaa_submitted_at' => now(),
                        'doaa_user_id' => $user->id,
                        'approved_by_id' => $user->id,
                        'approved_extended_until_date' => $isRecommended ? ($request->approved_extended_until_date ?? $extension->extended_until_date) : null,
                        'status' => $isRecommended ? 'approved' : 'rejected',
                        'current_stage' => 'completed',
                    ];
                    break;
            }
        }

        $extension->update($updateData);

        $prefix = ($extension->thesis?->student && $extension->thesis->student->isPhd()) ? 'PTS' : 'MSRTS';
        return redirect()->route('dashboard')->with('success', "{$prefix}-4 Extension Application evaluated and submitted successfully.");
    }

    // Handle Pop-Up Modal Reversion (All Authorities Except Academic Office)
    public function revert(Request $request, Pts4Extension $pts4Extension)
    {
        $this->authorize('review', $pts4Extension);

        $extension = $pts4Extension;
        $user = Auth::user();

        $userRole = Thesis::determineExtensionUserRole($user, $extension);
        if (!$userRole || $userRole === 'academic_office' || $extension->current_stage === 'academic_office') {
            return back()->with('error', 'Academic Office cannot revert extension applications.');
        }

        $request->validate([
            'reversion_comment' => 'required|string|max:2000',
        ]);

        $extension->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $userRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $request->reversion_comment,
        ]);

        $prefix = ($extension->thesis?->student && $extension->thesis->student->isPhd()) ? 'PTS' : 'MSRTS';
        return redirect()->route('dashboard')->with('success', "{$prefix}-4 Extension request reverted back to the student.");
    }
}

