<?php

namespace App\Http\Controllers;

use App\Models\Pts2Form;
use App\Models\User;
use App\Services\PtsDocumentService;
use App\Http\Requests\Pts2\UpdatePts2SupervisorRequest;
use App\Http\Requests\Pts2\EndorsePts2Request;
use App\Http\Requests\Pts2\RevertPts2Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Pts2Controller extends Controller
{
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }
    // Show the Main Supervisor review & edit form for a PTS-2 submission.
    public function review(Pts2Form $pts2)
    {
        $this->authorize('supervisorEdit', $pts2);

        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts2.review', compact('pts2', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Certifications, Evaluation & Endorsement).
    public function update(UpdatePts2SupervisorRequest $request, Pts2Form $pts2)
    {
        $thesis = $pts2->thesis;
        $validated = $request->validated();

        // Update active thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        // Optional Synopsis Replacement by Main Supervisor
        $msSynopsisPath = $pts2->main_supervisor_synopsis_report_doc_path;
        if ($request->hasFile('synopsis_report_doc')) {
            $msSynopsisPath = $this->ptsDocService->storeInProgressDocument($request->file('synopsis_report_doc'), $thesis->student->roll_number, $thesis->id, 'pts2', 'Synopsis_Report', 'Supervisor_Modified');
        }

        // Check if any co-supervisors exist
        $hasCoSup = false;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $hasCoSup = true;
                break;
            }
        }
        $nextStage = $hasCoSup ? 'co_supervisors' : 'academic_office';

        $pts2->update([
            'main_supervisor_thesis_title' => $validated['thesis_title'],
            'main_supervisor_synopsis_report_doc_path' => $msSynopsisPath,
            'main_supervisor_cert_prima_facie_case' => $request->boolean('cert_prima_facie_case'),
            'main_supervisor_cert_no_prior_degree_submission' => $request->boolean('cert_no_prior_degree_submission'),
            'main_supervisor_collaborative_work_status' => $request->boolean('collaborative_work_status'),
            'main_supervisor_collaborative_work_details' => $request->boolean('collaborative_work_status') ? $validated['collaborative_work_details'] : null,
            'main_supervisor_recommendation' => $request->boolean('recommendation'),
            'main_supervisor_student_comment' => $validated['main_supervisor_student_comment'] ?? null,
            'main_supervisor_confidential_remark' => $validated['main_supervisor_confidential_remark'] ?? null,
            'main_supervisor_submitted_at' => now(),
            'current_stage' => $nextStage,
            'status' => 'in_progress',
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-2 Form endorsed and forwarded successfully.');
    }

    // Display dedicated full-page review & endorsement view for PTS-2 with complete audit trail.
    public function reviewEndorse(Pts2Form $pts2)
    {
        $this->authorize('evaluate', $pts2);

        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $coSupervisors[$i] = User::find($pts2->$col);
            }
        }

        $academicOffice = $user->isAcademicOffice();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        return view('pts2.review_endorse', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'academicOffice',
            'actingDoaaUsers'
        ));
    }

    // Handle Endorsement of PTS-2 by evaluating authorities.
    public function endorse(EndorsePts2Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $stage = $pts2->current_stage;
        $validated = $request->validated();

        if ($stage === 'academic_office') {
            if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                return back()->with('error', 'Unauthorized access.');
            }

            $validated = $request->validate([
                'verified_details' => 'required|accepted',
                'verification_remark' => 'required|string|max:2000',
                'academic_office_course_credits' => 'required|numeric|min:0',
                'acting_doaa_email' => 'nullable|email',
            ]);

            $academicOfficeCourseCredits = (float)$validated['academic_office_course_credits'];
            $actingDoaaEmail = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;

            $pts2->update([
                'academic_office_is_verified' => true,
                'academic_office_verification_remark' => $validated['verification_remark'],
                'academic_office_course_credits' => $academicOfficeCourseCredits,
                'academic_office_submitted_at' => now(),
                'academic_office_user_id' => $user->id,
                'acting_doaa_email' => $actingDoaaEmail,
                'current_stage' => 'doaa',
            ]);

            // If Academic Office provides verified/adjusted course credits, override student's course credits earned
            if ($academicOfficeCourseCredits !== null) {
                $thesis->student->update([
                    'course_credits_earned' => $academicOfficeCourseCredits,
                ]);
            }
        } elseif ($stage === 'main_supervisor') {
            $isMainSupervisor = $thesis->student->isMainSupervisor($user);
            if (!$isMainSupervisor) {
                return back()->with('error', 'Unauthorized access.');
            }

            $validated = $request->validate([
                'cert_prima_facie_case' => 'required|boolean',
                'cert_no_prior_degree_submission' => 'required|boolean',
                'collaborative_work_status' => 'required|boolean',
                'collaborative_work_details' => $request->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
                'recommendation' => 'required|boolean',
                'student_comment' => 'nullable|string|max:2000',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
            ]);

            $isRecommended = $request->boolean('recommendation');
            $comment = $validated['student_comment'] ?? null;
            $remark = $validated['confidential_remark'] ?? null;

            $hasCoSup = false;
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts2->$col) {
                    $hasCoSup = true;
                    break;
                }
            }
            $nextStage = $hasCoSup ? 'co_supervisors' : 'academic_office';

            $pts2->update([
                'main_supervisor_cert_prima_facie_case' => $request->boolean('cert_prima_facie_case'),
                'main_supervisor_cert_no_prior_degree_submission' => $request->boolean('cert_no_prior_degree_submission'),
                'main_supervisor_collaborative_work_status' => $request->boolean('collaborative_work_status'),
                'main_supervisor_collaborative_work_details' => $request->boolean('collaborative_work_status') ? $validated['collaborative_work_details'] : null,
                'main_supervisor_recommendation' => $isRecommended,
                'main_supervisor_student_comment' => $comment,
                'main_supervisor_confidential_remark' => $remark,
                'main_supervisor_submitted_at' => now(),
                'current_stage' => $nextStage,
            ]);
        } else {
            $validated = $request->validate([
                'recommendation' => 'required|boolean',
                'student_comment' => 'nullable|string|max:2000',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
            ]);
            $isRecommended = $request->boolean('recommendation');
            $comment = $validated['student_comment'] ?? null;
            $remark = $validated['confidential_remark'] ?? null;

            switch ($stage) {
                case 'co_supervisors':
                    $roleKey = null;
                    for ($i = 1; $i <= 10; $i++) {
                        $col = "co_supervisor_{$i}_id";
                        if ($pts2->$col === $user->id) {
                            $roleKey = $i;
                            break;
                        }
                    }

                    if (!$roleKey) {
                        return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                    }

                    $pts2->update([
                        "co_supervisor_{$roleKey}_recommendation" => $isRecommended,
                        "co_supervisor_{$roleKey}_student_comment" => $comment,
                        "co_supervisor_{$roleKey}_confidential_remark" => $remark,
                        "co_supervisor_{$roleKey}_submitted_at" => now(),
                    ]);

                    // Check if all assigned co-supervisors have submitted recommendations
                    $allCoDone = true;
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts2->$idCol && is_null($pts2->$recCol)) {
                            $allCoDone = false;
                            break;
                        }
                    }

                    if ($allCoDone) {
                        $pts2->update([
                            'current_stage' => 'academic_office',
                            'co_supervisors_submitted_at' => now(),
                        ]);
                    }
                    break;

                case 'doaa':
                    if (!($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)))) {
                        return back()->with('error', 'Unauthorized access.');
                    }
                    $pts2->update([
                        'doaa_student_comment' => $comment,
                        'doaa_approval' => $isRecommended,
                        'doaa_confidential_remark' => $remark,
                        'doaa_submitted_at' => now(),
                        'doaa_user_id' => $user->id,
                        'approved_by_authority' => $user->email,
                        'current_stage' => 'completed',
                        'status' => $isRecommended ? 'approved' : 'rejected',
                    ]);

                    if ($isRecommended) {
                        $this->ptsDocService->moveToApproved($pts2, 'pts2');
                    } else {
                        $this->ptsDocService->moveToRejected($pts2, 'pts2');
                    }
                    break;

                default:
                    return back()->with('error', 'Invalid stage for endorsement.');
            }
        }

        return redirect()->route('dashboard')->with('success', 'PTS-2 form evaluated and submitted successfully!');
    }

    // Universal Pop-up Reversion action for all evaluating authorities.
    public function revert(RevertPts2Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;

        if ($user->isAcademicOffice() || $pts2->current_stage === 'academic_office') {
            return back()->with('error', 'Academic Office cannot revert forms; verification and forwarding only.');
        }

        $validated = $request->validated();

        $role = $pts2->current_stage;
        if ($role === 'co_supervisors') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts2->$col === $user->id) {
                    $role = "co_supervisor_{$i}";
                    break;
                }
            }
        }

        $pts2->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $role,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        $this->ptsDocService->moveToReverted($pts2, 'pts2');

        $redirectRoute = $user->isExternalSupervisor() ? 'external_supervisor.dashboard' : ($user->isFaculty() ? 'faculty.dashboard' : 'dashboard');
        return redirect()->route($redirectRoute)->with('warning', 'PTS-2 Synopsis Form has been reverted back to the student.');
    }

    // Show read-only details of an approved/rejected PTS-2 form.
    public function show(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        // Authorization check: User must be authorized to view this submission
        if (!$pts2->canUserView($user)) {
            abort(403, 'Unauthorized access to view this submission.');
        }

        if ($pts2->status === 'in_progress') {
            return redirect()->route('pts2.submitted', $pts2->id);
        }

        if ($pts2->status === 'reverted') {
            return redirect()->route('pts2.reverted', $pts2->id);
        }

        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $coSupervisors[$i] = User::find($pts2->$col);
            }
        }

        return view('pts2.show', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }

    // Show dedicated view for reverted PTS-2 form.
    public function reverted(Pts2Form $pts2)
    {
        $user = auth()->user();

        // Status Guardrails
        if ($pts2->status === 'in_progress') {
            return redirect()->route('pts2.submitted', $pts2->id);
        }

        if (in_array($pts2->status, ['approved', 'rejected'])) {
            return redirect()->route('pts2.show', $pts2->id);
        }

        // Student owner redirects to student form creation/edit; other students blocked
        if ($user->isStudent()) {
            if ($pts2->thesis?->student?->user_id === $user->id) {
                return redirect()->route('student.pts2.create');
            }
            abort(403, 'Unauthorized access to reverted PTS-2 view.');
        }

        if (!$pts2->canUserViewRevertedForm($user)) {
            return redirect()->back()->with('warning', 'This PTS-2 form was reverted by ' . $pts2->getRevertedByRoleLabel() . ' and is not accessible at your review stage until resubmitted.');
        }

        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;
        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $coSupervisors[$i] = User::find($pts2->$col);
            }
        }

        return view('pts2.reverted', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }

    // Display view of submitted PTS-2 form strictly scoped to viewing user's submission state.
    public function submitted(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;

        // Status Guardrails
        if (in_array($pts2->status, ['approved', 'rejected'])) {
            return redirect()->route('pts2.show', $pts2->id);
        }

        if ($pts2->status === 'reverted') {
            return redirect()->route('pts2.reverted', $pts2->id);
        }

        // Check workflow stage progression & access
        $accessStatus = $pts2->getUserSubmissionAccessStatus($user);
        if ($accessStatus === 'pending_endorsement') {
            if ($student->isMainSupervisor($user) && $pts2->current_stage === 'main_supervisor') {
                return redirect()->route('faculty.pts2.review', $pts2->id);
            }
            return redirect()->route('pts2.review', $pts2->id);
        }
        if ($accessStatus === 'not_reached' || $accessStatus === 'unauthorized') {
            abort(403, 'This submission has not reached your review stage yet.');
        }

        // Role checks
        $isOwnerStudent = ($user->isStudent() && $student->user_id === $user->id);
        $isMainSupervisor = $student->isMainSupervisor($user);
        
        // Find if user is a specific co-supervisor
        $myCoSupSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col == $user->id) {
                $myCoSupSlot = $i;
                break;
            }
        }

        $isAcademicOffice = $user->isAcademicOffice();
        $isDpgc = $user->isDpgc();
        $isHod = $user->isHod();
        $isDoaa = ($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)));

        $studentUser = $student->user;
        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $coSupervisors[$i] = User::find($pts2->$col);
            }
        }

        // Determine viewPerspective: 'student', 'main_supervisor', 'co_supervisor', 'dpgc', 'hod', 'academic_office', 'doaa'
        $viewPerspective = 'student';
        if ($isOwnerStudent) {
            $viewPerspective = 'student';
        } elseif ($isMainSupervisor) {
            $viewPerspective = 'main_supervisor';
        } elseif ($myCoSupSlot !== null) {
            $viewPerspective = 'co_supervisor';
        } elseif ($isDpgc) {
            $viewPerspective = 'dpgc';
        } elseif ($isHod) {
            $viewPerspective = 'hod';
        } elseif ($isAcademicOffice) {
            $viewPerspective = 'academic_office';
        } elseif ($isDoaa) {
            $viewPerspective = 'doaa';
        }

        return view('pts2.submitted', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'viewPerspective',
            'myCoSupSlot'
        ));
    }
}
