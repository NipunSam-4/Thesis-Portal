<?php

namespace App\Http\Controllers;

use App\Models\Pts3Form;
use App\Models\Pts3Examiner;
use App\Models\Pts3OebMember;
use App\Models\Student;
use App\Models\User;
use App\Models\Thesis;
use App\Http\Requests\Pts3\StorePts3Request;
use App\Http\Requests\Pts3\UpdatePts3Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pts3Controller extends Controller
{
    // Show Main Supervisor initiation form
    public function create(Student $student)
    {
        $user = auth()->user();
        if (!$student->isMainSupervisor($user)) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-3 initiation.');
        }

        $thesis = $student->activeThesis ?? $student->thesis;
        if (!$thesis) {
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        if ($thesis->status === 'completed') {
            return redirect()->route('faculty.dashboard')->with('info', 'This student thesis has already been completed.');
        }

        // Check if a PTS-3 form already exists and isn't reverted/rejected
        $existing = Pts3Form::where('thesis_id', $thesis->id)
            ->whereNotIn('status', ['rejected', 'reverted'])
            ->first();

        if ($existing) {
            return redirect()->route('pts3.show', $existing)->with('warning', 'A PTS-3 submission already exists for this student.');
        }

        $studentUser = $student->user;

        return view('faculty.pts3.create', compact('student', 'thesis', 'studentUser'));
    }

    // Process initial PTS-3 form submission by Main Supervisor
    public function store(StorePts3Request $request, Student $student)
    {
        $user = auth()->user();
        $thesis = $student->activeThesis ?? $student->thesis;
        if (!$thesis) {
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }

        if ($thesis->status === 'completed') {
            return redirect()->route('faculty.dashboard')->with('error', 'This student thesis has already been completed.');
        }

        $validated = $request->validated();

        $thesis = $student->activeThesis ?? $student->thesis;
        if (!$thesis) {
            return redirect()->route('faculty.dashboard')->with('error', 'No thesis registered for this student.');
        }
        $thesis->update(['title' => $validated['thesis_title']]);

        // Map co-supervisors
        $coSupervisors = $student->allCoSupervisors()->pluck('id')->all();
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
                        $docPath = $request->file("{$key}.{$index}.consent_doc")->store("pts3_consents/{$student->roll_number}", 'local');
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

    // Unified Confidential View Action (Handles in_progress, approved, rejected, reverted)
    public function show(Pts3Form $pts3)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $userRank = $this->getUserRank($user, $pts3);

        if ($userRank === 0) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to confidential PTS-3 form.');
        }

        // If form is reverted, check trail visibility
        if ($pts3->status === 'reverted' && !$pts3->canUserViewRevertedForm($user)) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this reverted PTS-3 form trail.');
        }

        $canEvaluate = $this->canUserEvaluate($user, $pts3);
        $thesis = $pts3->thesis;
        $student = $thesis?->student;
        
        $studentUser = $student?->user;

        // Fetch examiners & OEB members
        $indianExaminers = $pts3->indianExaminers()->get();
        $internationalExaminers = $pts3->internationalExaminers()->get();
        $oebMembers = $pts3->oebMembers()->get();

        return view('pts3.show', compact(
            'pts3',
            'thesis',
            'student',
            'studentUser',
            'userRank',
            'canEvaluate',
            'indianExaminers',
            'internationalExaminers',
            'oebMembers'
        ));
    }

    // Endorse action for active evaluating authority
    public function endorse(Request $request, Pts3Form $pts3)
    {
        $this->authorize('evaluate', $pts3);

        $user = auth()->user();
        $stage = $pts3->current_stage;
        $userRank = $this->getUserRank($user, $pts3);

        DB::beginTransaction();
        try {
            if ($stage === 'co_supervisors') {
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
            } elseif ($stage === 'dpgc') {
                $pts3->dpgc_recommendation = true;
                $pts3->dpgc_submitted_at = now();
                $pts3->dpgc_user_id = $user->id;
                $pts3->current_stage = 'hod';
                $pts3->save();
            } elseif ($stage === 'hod') {
                $pts3->hod_recommendation = true;
                $pts3->hod_submitted_at = now();
                $pts3->hod_user_id = $user->id;
                $pts3->current_stage = 'academic_office';
                $pts3->save();
            } elseif ($stage === 'academic_office') {
                $pts3->academic_office_is_verified = true;
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
            } elseif ($stage === 'doaa') {
                $pts3->doaa_is_verified = true;
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
            } elseif ($stage === 'senate_chairperson') {
                $isApproved = $request->input('decision') === 'approve';

                $pts3->senate_chairperson_approval = $isApproved;
                $pts3->senate_chairperson_submitted_at = now();
                $pts3->senate_chairperson_user_id = $user->id;
                $pts3->senate_chairperson_approval_remark = $request->input('senate_chairperson_approval_remark');
                $pts3->senate_chairperson_confidential_remark = $request->input('senate_chairperson_confidential_remark');
                $pts3->status = $isApproved ? 'approved' : 'rejected';
                $pts3->current_stage = 'completed';
                $pts3->approved_by_authority = $isApproved ? 'Senate Chairperson' : null;
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
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving evaluation: ' . $e->getMessage());
        }

        return redirect()->route('pts3.show', $pts3)->with('success', 'Evaluation submitted successfully.');
    }

    // Revert form back to Main Supervisor
    public function revert(Request $request, Pts3Form $pts3)
    {
        $this->authorize('revert', $pts3);

        $user = auth()->user();
        $validated = $request->validate([
            'reversion_comment' => 'required|string|max:2000',
        ]);

        $reverterRole = $this->getUserRoleCode($user, $pts3);

        $pts3->update([
            'status' => 'reverted',
            'current_stage' => 'main_supervisor',
            'reverted_by_role' => $reverterRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        return redirect()->route('pts3.show', $pts3)->with('warning', 'PTS-3 form has been reverted to the Main Supervisor.');
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
        if ($student) {
            for ($i = 1; $i <= 10; $i++) {
                if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                    return 2;
                }
            }
        }

        // 3. DPGC
        if ($user->role === 'dpgc' && $user->department_id === $student?->department_id) {
            return 3;
        }

        // 4. HOD
        if ($user->role === 'hod' && $user->department_id === $student?->department_id) {
            return 4;
        }

        // 5. Academic Office
        if ($user->role === 'academic_office') {
            return 5;
        }

        // 6. DOAA / Vested DOAA / Acting DOAA
        if ($user->isDoaa() || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email) || ($pts3->acting_doaa_email && $user->email === $pts3->acting_doaa_email)) {
            return 6;
        }

        // 7. Senate Chairperson
        if ($user->role === 'senate_chairperson') {
            return 7;
        }

        // Fallback for Admin or authorized global roles
        if (auth('admin')->check() || $user->isAdmin()) {
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

    // Helper: Check if user is currently authorized to evaluate
    protected function canUserEvaluate(User $user, Pts3Form $pts3): bool
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
}
