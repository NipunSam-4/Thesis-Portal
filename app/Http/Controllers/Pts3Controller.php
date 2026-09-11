<?php

namespace App\Http\Controllers;

use App\Models\Pts3Form;
use App\Models\Pts3Examiner;
use App\Models\Pts3OebMember;
use App\Models\Student;
use App\Models\VestedDoaa;
use App\Models\User;
use App\Models\Thesis;
use App\Http\Requests\Pts3\StorePts3Request;
use App\Http\Requests\Pts3\UpdatePts3Request;
use App\Http\Requests\Pts3\RevertPts3Request;
use App\Services\PtsDocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pts3Controller extends Controller
{
    use AuthorizesRequests;

    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Show Main Supervisor initiation form
    public function create(Student $student)
    {
        $user = auth()->user();
        if (!$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 initiation.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('faculty.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        // Check if a reverted PTS-3 form exists for this student
        $reverted = Pts3Form::where('thesis_id', $thesis->id)
            ->where('status', 'reverted')
            ->first();

        if ($reverted) {
            return redirect()->route('faculty.pts3.edit', $reverted->id);
        }

        // Check if a PTS-3 form already exists and is in progress or approved
        $existing = Pts3Form::where('thesis_id', $thesis->id)
            ->whereNotIn('status', ['rejected', 'reverted'])
            ->first();

        if ($existing) {
            return redirect()->route('pts3.show', $existing)->with('warning', 'A PTS-3 submission already exists for this student.');
        }

        $studentUser = $student->user;

        return view('faculty.pts3.create', compact('user','student', 'thesis', 'studentUser'));
    }

    // Process initial PTS-3 form submission by Main Supervisor
    public function store(StorePts3Request $request, Student $student)
    {
        $user = auth()->user();
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('faculty.dashboard')->with('error', 'This thesis has already been completed.');
            }
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        $validated = $request->validated();
        $thesis->update(['title' => $validated['thesis_title']]);

        // Map active co-supervisors
        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
        }

        $nextStage = count($coSupervisors) > 0 ? 'co_supervisors' : 'dpgc';

        DB::beginTransaction();
        try {
            $pts3 = Pts3Form::create(array_merge([
                'thesis_id' => $thesis->id,
                'thesis_title' => $validated['thesis_title'],
                
                'main_supervisor_id' => $user->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => now(),
                
                'current_stage' => $nextStage,
                'status' => 'in_progress',
            ], $coSupData));

            // Save Examiners
            foreach (['indian_examiners' => 'indian', 'international_examiners' => 'international'] as $key => $type) {
                foreach ($validated[$key] as $index => $examinerData) {
                    $docPath = null;
                    if ($request->hasFile("{$key}.{$index}.consent_doc")) {
                        $docPath = $this->ptsDocService->handleInProgressFile(
                            $request->file("{$key}.{$index}.consent_doc"),
                            null,
                            $student->roll_number,
                            $thesis->id,
                            'pts3',
                            "Consent_{$type}_" . ($index + 1),
                            'Main_Supervisor'
                        );
                    }

                    Pts3Examiner::create([
                        'pts3_form_id' => $pts3->id,
                        'type' => $type,
                        'name' => $examinerData['name'],
                        'designation' => $examinerData['designation'],
                        'organization' => $examinerData['organization'],
                        'postal_address' => $examinerData['postal_address'],
                        'email' => $examinerData['email'],
                        'phone_number' => $examinerData['phone_number'],
                        'phone_country_code' => $examinerData['phone_country_code'] ?? '+91',
                        'phone_iso2' => $examinerData['phone_iso2'] ?? 'in',
                        'website' => $examinerData['website'] ?? null,
                        'research_area' => $examinerData['research_area'] ?? null,
                        'has_consent' => $examinerData['has_consent'],
                        'consent_doc_path' => $docPath,
                    ]);
                }
            }

            // Save OEB Members
            foreach ($validated['oeb_members'] as $oeb) {
                Pts3OebMember::create([
                    'pts3_form_id' => $pts3->id,
                    'name' => $oeb['name'],
                    'designation' => $oeb['designation'],
                    'department' => $oeb['department'],
                    'email' => $oeb['email'],
                    'phone_number' => $oeb['phone_number'] ?? null,
                    'phone_country_code' => $oeb['phone_country_code'] ?? '+91',
                    'phone_iso2' => $oeb['phone_iso2'] ?? 'in',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error initiating PTS-3 form: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-3 initiated successfully.');
    }

    // Show edit/resubmission form for reverted PTS-3 form
    public function edit(Pts3Form $pts3)
    {
        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$student || !$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 edit.');
        }

        if ($thesis?->status === 'completed') {
            return redirect()->route('pts3.show', $pts3->id)->with('info', 'This thesis has already been completed.');
        }

        if ($pts3->status !== 'reverted') {
            return redirect()->route('pts3.show', $pts3->id)->with('info', 'Only reverted PTS-3 forms can be edited.');
        }

        $studentUser = $student->user;

        return view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'pts3'));
    }

    // Process update and resubmission of reverted PTS-3 form
    public function update(UpdatePts3Request $request, Pts3Form $pts3)
    {
        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$student || !$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 update.');
        }

        if ($thesis?->status === 'completed') {
            return redirect()->route('pts3.show', $pts3->id)->with('error', 'This thesis has already been completed.');
        }

        if ($pts3->status !== 'reverted') {
            return redirect()->route('pts3.show', $pts3->id)->with('error', 'Only reverted PTS-3 forms can be updated.');
        }

        $validated = $request->validated();
        $thesis->update(['title' => $validated['thesis_title']]);

        // Map active co-supervisors
        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
            $coSupData["co_supervisor_{$i}_recommendation"] = null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = null;
        }

        $nextStage = count($coSupervisors) > 0 ? 'co_supervisors' : 'dpgc';

        DB::beginTransaction();
        try {
            // Create a brand new PTS-3 form row, preserving historical reverted submissions
            $newPts3 = Pts3Form::create(array_merge([
                'thesis_id' => $thesis->id,
                'thesis_title' => $validated['thesis_title'],
                'main_supervisor_id' => $user->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => now(),
                'current_stage' => $nextStage,
                'status' => 'in_progress',
            ], $coSupData));

            // Create Examiners for the new form
            foreach (['indian_examiners' => 'indian', 'international_examiners' => 'international'] as $key => $type) {
                foreach ($validated[$key] as $index => $examinerData) {
                    $docPath = null;
                    $existingDocPath = $examinerData['consent_doc_path'] ?? null;
                    if ($request->hasFile("{$key}.{$index}.consent_doc")) {
                        $docPath = $this->ptsDocService->handleInProgressFile(
                            $request->file("{$key}.{$index}.consent_doc"),
                            null,
                            $student->roll_number,
                            $thesis->id,
                            'pts3',
                            "Consent_{$type}_" . ($index + 1),
                            'Main_Supervisor'
                        );
                    } elseif (!empty($existingDocPath)) {
                        $docPath = $this->ptsDocService->handleInProgressFile(
                            null,
                            $existingDocPath,
                            $student->roll_number,
                            $thesis->id,
                            'pts3',
                            "Consent_{$type}_" . ($index + 1),
                            'Main_Supervisor'
                        );
                    }

                    Pts3Examiner::create([
                        'pts3_form_id' => $newPts3->id,
                        'type' => $type,
                        'name' => $examinerData['name'],
                        'designation' => $examinerData['designation'],
                        'organization' => $examinerData['organization'],
                        'postal_address' => $examinerData['postal_address'],
                        'email' => $examinerData['email'],
                        'phone_number' => $examinerData['phone_number'],
                        'phone_country_code' => $examinerData['phone_country_code'] ?? '+91',
                        'phone_iso2' => $examinerData['phone_iso2'] ?? 'in',
                        'website' => $examinerData['website'] ?? null,
                        'research_area' => $examinerData['research_area'] ?? null,
                        'has_consent' => $examinerData['has_consent'],
                        'consent_doc_path' => $docPath,
                    ]);
                }
            }

            // Create OEB Members for the new form
            foreach ($validated['oeb_members'] as $oeb) {
                Pts3OebMember::create([
                    'pts3_form_id' => $newPts3->id,
                    'name' => $oeb['name'],
                    'designation' => $oeb['designation'],
                    'department' => $oeb['department'],
                    'email' => $oeb['email'],
                    'phone_number' => $oeb['phone_number'] ?? null,
                    'phone_country_code' => $oeb['phone_country_code'] ?? '+91',
                    'phone_iso2' => $oeb['phone_iso2'] ?? 'in',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error resubmitting PTS-3 form: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-3 resubmitted successfully.');
    }

    // Display dedicated evaluation/review portal for active authority
    public function review(Pts3Form $pts3)
    {
        $this->authorize('review', $pts3);

        $user = auth()->user();
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if ($student && $student->isMainSupervisor($user) && $pts3->current_stage === 'main_supervisor') {
            return redirect()->route('faculty.pts3.edit', $pts3->id);
        }

        $student?->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student?->user;

        $userRank = $this->getUserRank($user, $pts3);
        $coSupervisors = $pts3->getCoSupervisors();

        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        // Fetch examiners & OEB members ordered by evaluating authority's role
        $indianExaminers = $this->getOrderedExaminers($pts3, $userRank, 'indian');
        $internationalExaminers = $this->getOrderedExaminers($pts3, $userRank, 'international');
        $oebMembers = $this->getOrderedOebMembers($pts3, $userRank);

        return view('pts3.review', compact(
            'pts3',
            'thesis',
            'student',
            'studentUser',
            'user',
            'userRank',
            'coSupervisors',
            'actingDoaaUsers',
            'indianExaminers',
            'internationalExaminers',
            'oebMembers'
        ));
    }

    // Endorse action for active evaluating authority
    public function endorse(Request $request, Pts3Form $pts3)
    {
        $this->authorize('review', $pts3);

        $request->validate([
            'declaration' => 'required|accepted',
        ], [
            'declaration.required' => 'Please confirm the declaration checkbox before submitting.',
            'declaration.accepted' => 'Please confirm the declaration checkbox before submitting.',
        ]);

        $user = auth()->user();
        $stage = $pts3->current_stage;
        $userRank = $this->getUserRank($user, $pts3);

        DB::beginTransaction();
        try {
            switch ($stage) {
                case 'co_supervisors':
                    for ($i = 1; $i <= 10; $i++) {
                        if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                            $pts3->{"co_supervisor_{$i}_recommendation"} = true;
                            $pts3->{"co_supervisor_{$i}_submitted_at"} = now();
                            break;
                        }
                    }

                    // Check if all co-supervisors have submitted
                    $allSubmitted = true;
                    for ($i = 1; $i <= 10; $i++) {
                        $coId = $pts3->{"co_supervisor_{$i}_id"};
                        if ($coId && $pts3->{"co_supervisor_{$i}_recommendation"} === null) {
                            $allSubmitted = false;
                            break;
                        }
                    }

                    if ($allSubmitted) {
                        $pts3->co_supervisors_submitted_at = now();
                        $pts3->current_stage = 'dpgc';
                    }
                    $pts3->save();
                    break;

                case 'dpgc':
                    $pts3->dpgc_recommendation = true;
                    $pts3->dpgc_submitted_at = now();
                    $pts3->dpgc_user_id = $user->id;
                    $pts3->current_stage = 'hod';
                    $pts3->save();
                    break;

                case 'hod':
                    $pts3->hod_recommendation = true;
                    $pts3->hod_submitted_at = now();
                    $pts3->hod_user_id = $user->id;
                    $pts3->current_stage = 'academic_office';
                    $pts3->save();
                    break;

                case 'academic_office':
                    $pts3->academic_office_is_verified = true;
                    $pts3->academic_office_verification_remark = $request->input('academic_office_verification_remark');
                    $pts3->acting_doaa_email = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;
                    $pts3->academic_office_submitted_at = now();
                    $pts3->academic_office_user_id = $user->id;
                    $pts3->current_stage = 'doaa';
                    $pts3->save();

                    // Save any Academic Office examiner/OEB remarks if provided
                    if ($request->has('examiner_remarks')) {
                        foreach ($request->input('examiner_remarks') as $exId => $remark) {
                            Pts3Examiner::where('id', $exId)->where('pts3_form_id', $pts3->id)->update(['academic_office_remark' => $remark]);
                        }
                    }
                    if ($request->has('oeb_remarks')) {
                        foreach ($request->input('oeb_remarks') as $oebId => $remark) {
                            Pts3OebMember::where('id', $oebId)->where('pts3_form_id', $pts3->id)->update(['academic_office_remark' => $remark]);
                        }
                    }
                    break;

                case 'doaa':
                    $pts3->doaa_is_verified = true;
                    $pts3->doaa_verification_remark = $request->input('doaa_verification_remark');
                    $pts3->doaa_submitted_at = now();
                    $pts3->doaa_user_id = $user->id;
                    $pts3->current_stage = 'senate_chairperson';
                    $pts3->save();

                    // Save DOAA priorities and remarks
                    if ($request->has('doaa_examiner_priority')) {
                        foreach ($request->input('doaa_examiner_priority') as $exId => $prio) {
                            Pts3Examiner::where('id', $exId)->where('pts3_form_id', $pts3->id)->update(['doaa_priority' => $prio ?: null]);
                        }
                    }
                    if ($request->has('doaa_examiner_remarks')) {
                        foreach ($request->input('doaa_examiner_remarks') as $exId => $remark) {
                            Pts3Examiner::where('id', $exId)->where('pts3_form_id', $pts3->id)->update(['doaa_remark' => $remark]);
                        }
                    }
                    if ($request->has('doaa_oeb_priority')) {
                        foreach ($request->input('doaa_oeb_priority') as $oebId => $prio) {
                            Pts3OebMember::where('id', $oebId)->where('pts3_form_id', $pts3->id)->update(['doaa_priority' => $prio ?: null]);
                        }
                    }
                    if ($request->has('doaa_oeb_remarks')) {
                        foreach ($request->input('doaa_oeb_remarks') as $oebId => $remark) {
                            Pts3OebMember::where('id', $oebId)->where('pts3_form_id', $pts3->id)->update(['doaa_remark' => $remark]);
                        }
                    }
                    break;

                case 'senate_chairperson':
                    $isApproved = $request->input('recommendation') === '1' || $request->input('decision') === 'approve';
                    $approvalRemark = $request->input('senate_chairperson_approval_remark') ?? $request->input('confidential_remark');

                    if (!$isApproved && empty(trim($approvalRemark ?? ''))) {
                        DB::rollBack();
                        return back()->with('error', 'Non-approval remark is mandatory when not approving the form.');
                    }

                    $pts3->senate_chairperson_approval = $isApproved;
                    $pts3->senate_chairperson_submitted_at = now();
                    $pts3->senate_chairperson_user_id = $user->id;
                    $pts3->senate_chairperson_approval_remark = $approvalRemark;
                    $pts3->senate_chairperson_confidential_remark = $request->input('senate_chairperson_confidential_remark');
                    $pts3->status = $isApproved ? 'approved' : 'rejected';
                    $pts3->current_stage = 'completed';
                    $pts3->approved_by_id = $isApproved ? $user->id : null;
                    $pts3->save();

                    // Save Senate Chairperson priorities
                    if ($request->has('senate_examiner_priority')) {
                        foreach ($request->input('senate_examiner_priority') as $exId => $prio) {
                            Pts3Examiner::where('id', $exId)->where('pts3_form_id', $pts3->id)->update(['senate_chairperson_priority' => $prio ?: null]);
                        }
                    }
                    if ($request->has('senate_oeb_priority')) {
                        foreach ($request->input('senate_oeb_priority') as $oebId => $prio) {
                            Pts3OebMember::where('id', $oebId)->where('pts3_form_id', $pts3->id)->update(['senate_chairperson_priority' => $prio ?: null]);
                        }
                    }

                    if ($isApproved) {
                        $this->ptsDocService->moveToApproved($pts3, 'pts3');
                    } else {
                        $this->ptsDocService->moveToRejected($pts3, 'pts3');
                    }
                    break;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving evaluation: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'PTS-3 evaluation submitted successfully.');
    }

    // Revert form back to Main Supervisor
    public function revert(RevertPts3Request $request, Pts3Form $pts3)
    {
        $user = auth()->user();

        if ($pts3->status !== 'in_progress') {
            return back()->with('error', 'Cannot revert a PTS-3 form that is not currently in progress.');
        }

        $thesis = $pts3->thesis;
        $student = $thesis?->student;
        $stage = $pts3->current_stage;
        $validated = $request->validated();
        $revertedRole = null;

        if ($stage === 'dpgc') {
            if (!$user->isDpgc() || ($student && $user->deptAuthorityProfile && $user->deptAuthorityProfile->department_id !== $student->department_id)) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'dpgc';
        } elseif ($stage === 'hod') {
            if (!$user->isHod() || ($student && $user->deptAuthorityProfile && $user->deptAuthorityProfile->department_id !== $student->department_id)) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'hod';
        } elseif ($stage === 'academic_office') {
            if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'academic_office';
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts3->acting_doaa_email === $user->email || $pts3->vested_doaa_email === $user->email)) || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email))) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'doaa';
        } elseif ($stage === 'senate_chairperson') {
            if (!$user->isSenateChairperson()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'senate_chairperson';
        } else {
            return back()->with('error', 'Invalid stage for reversion. Only evaluating authorities (DPGC, HOD, Academic Office, DOAA, Senate Chairperson) can revert.');
        }

        $pts3->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        $this->ptsDocService->moveToReverted($pts3, 'pts3');

        return redirect()->route('dashboard')->with('warning', 'PTS-3 form has been reverted to the Main Supervisor.');
    }

    // Unified Confidential View Action (Handles in_progress, approved, rejected, reverted)
    public function show(Pts3Form $pts3)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Students are strictly barred from viewing confidential PTS-3 examiners
        if ($user->isStudent()) {
            return redirect()->route('student.dashboard')->with('error', 'Students are not authorized to access confidential PTS-3 form details.');
        }

        $userRank = $this->getUserRank($user, $pts3);

        if ($userRank === 0) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
        }

        // 1. If form is in_progress: authority can view show ONLY if they have already given their recommendation
        if ($pts3->status === 'in_progress') {
            $accessStatus = $pts3->getUserSubmissionAccessStatus($user);

            // If active review is pending for this user, redirect to review portal
            if ($accessStatus === 'pending_endorsement') {
                return redirect()->route('pts3.review', $pts3->id);
            }

            // If the form has not reached their stage yet, disallow viewing
            if ($accessStatus === 'not_reached') {
                return redirect()->route('dashboard')->with('error', 'This submission has not reached your review stage yet.');
            }

            if ($accessStatus !== 'allowed') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
            }
        }

        // 2. If form is reverted: check reverted trail visibility
        if ($pts3->status === 'reverted') {
            if (!$pts3->canUserViewRevertedForm($user)) {
                return redirect()->route('dashboard')->with('error', 'You are not authorized to view this reverted PTS-3 form trail.');
            }
        }

        // 3. If form is approved or rejected: check general viewing authorization
        if (in_array($pts3->status, ['approved', 'rejected'])) {
            if (!$pts3->canUserView($user)) {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
            }
        }

        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if (!$thesis || !$student) {
            return redirect()->route('dashboard')->with('error', 'Associated thesis or student record not found.');
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;
        $coSupervisors = $pts3->getCoSupervisors();

        // Fetch examiners & OEB members ordered by viewing authority's role
        $indianExaminers = $this->getOrderedExaminers($pts3, $userRank, 'indian');
        $internationalExaminers = $this->getOrderedExaminers($pts3, $userRank, 'international');
        $oebMembers = $this->getOrderedOebMembers($pts3, $userRank);

        return view('pts3.show', compact(
            'pts3',
            'thesis',
            'student',
            'studentUser',
            'user',
            'userRank',
            'coSupervisors',
            'indianExaminers',
            'internationalExaminers',
            'oebMembers'
        ));
    }


    // Helper: Determine integer rank of logged-in user for trail visibility
    protected function getUserRank(User $user, Pts3Form $pts3): int
    {
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        // 1. Main Supervisor
        if ($student && $student->isMainSupervisor($user)) {
            return 1;
        }

        // 2. Co-Supervisor / External Supervisor
        if ($student && $student->isCoSupervisor($user)) {
            return 2;
        }

        // 3. DPGC
        if ($user->isDpgc() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 3;
        }

        // 4. HOD
        if ($user->isHod() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 4;
        }

        // 5. Academic Office
        if ($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority() && !$user->isDoaa() && !$user->isSenateChairperson())) {
            return 5;
        }

        // 6. DOAA / Vested DOAA / Acting DOAA
        if ($user->isDoaa() || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email) || ($pts3->acting_doaa_email && $user->email === $pts3->acting_doaa_email)) {
            return 6;
        }

        // 7. Senate Chairperson
        if ( $user->isSenateChairperson()) {
            return 7;
        }

        return 0; // Unauthorized
    }

    // Helper: Determine role code for reversion tracking
    protected function getUserRoleCode(User $user, Pts3Form $pts3): string
    {
        $rank = $this->getUserRank($user, $pts3);
        return match ($rank) {
            1 => 'main_supervisor',
            2 => $user->isExternalSupervisor() ? 'external_supervisor' : 'co_supervisor',
            3 => 'dpgc',
            4 => 'hod',
            5 => 'academic_office',
            6 => 'doaa',
            7 => 'senate_chairperson',
            default => 'authority',
        };
    }

    // Helper: Check if user is currently authorized to review/endorse
    protected function canUserReview(User $user, Pts3Form $pts3): bool
    {
        if ($pts3->status !== 'in_progress') {
            return false;
        }

        $stage = $pts3->current_stage;
        $userRank = $this->getUserRank($user, $pts3);

        return match ($stage) {
            'main_supervisor' => ($userRank === 1),
            'co_supervisors' => ($userRank === 2 && $this->isCoSupervisorPending($user, $pts3)),
            'dpgc' => ($userRank === 3),
            'hod' => ($userRank === 4),
            'academic_office' => ($userRank === 5),
            'doaa' => ($userRank === 6),
            'senate_chairperson' => ($userRank === 7),
            default => false,
        };
    }

    protected function isCoSupervisorPending(User $user, Pts3Form $pts3): bool
    {
        for ($i = 1; $i <= 10; $i++) {
            if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                return $pts3->{"co_supervisor_{$i}_recommendation"} === null;
            }
        }
        return false;
    }

    // Helper: Get ordered examiners based on viewer role rank
    protected function getOrderedExaminers(Pts3Form $pts3, int $userRank, string $type)
    {
        $query = $pts3->examiners()->where('type', $type);
        if ($userRank === 6) {
            // DOAA priority order
            return $query->orderByRaw('doaa_priority IS NULL, doaa_priority ASC')->orderBy('id', 'ASC')->get();
        } elseif ($userRank === 7) {
            // Senate Chairperson priority order (inherits DOAA priority order if Senate Chairperson hasn't set custom priority)
            return $query->orderByRaw('senate_chairperson_priority IS NULL, senate_chairperson_priority ASC')
                ->orderByRaw('doaa_priority IS NULL, doaa_priority ASC')
                ->orderBy('id', 'ASC')
                ->get();
        }
        // Main Sup, Co-Sup, DPGC, HOD, Academic Office: Original submission order
        return $query->orderBy('id', 'ASC')->get();
    }

    // Helper: Get ordered OEB members based on viewer role rank
    protected function getOrderedOebMembers(Pts3Form $pts3, int $userRank)
    {
        $query = $pts3->oebMembers();
        if ($userRank === 6) {
            // DOAA priority order
            return $query->orderByRaw('doaa_priority IS NULL, doaa_priority ASC')->orderBy('id', 'ASC')->get();
        } elseif ($userRank === 7) {
            // Senate Chairperson priority order (inherits DOAA priority order if Senate Chairperson hasn't set custom priority)
            return $query->orderByRaw('senate_chairperson_priority IS NULL, senate_chairperson_priority ASC')
                ->orderByRaw('doaa_priority IS NULL, doaa_priority ASC')
                ->orderBy('id', 'ASC')
                ->get();
        }
        // Main Sup, Co-Sup, DPGC, HOD, Academic Office: Original submission order
        return $query->orderBy('id', 'ASC')->get();
    }
}
