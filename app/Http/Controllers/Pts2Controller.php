<?php

namespace App\Http\Controllers;

use App\Models\Pts2Form;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Pts2Controller extends Controller
{
    // Show the Main Supervisor review & edit form for a PTS-2 submission.
    public function edit(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;

        $isMainSupervisor = $thesis->student->isMainSupervisor($user);
        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-2 review.');
        }

        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts2.review', compact('pts2', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Certifications, Evaluation & Endorsement).
    public function update(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;

        $isMainSupervisor = $thesis->student->isMainSupervisor($user);
        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-2 review.');
        }

        $validated = $request->validate([
            'cert_prima_facie_case' => 'required|boolean',
            'cert_no_prior_degree_submission' => 'required|boolean',
            'collaborative_work_status' => 'required|boolean',
            'collaborative_work_details' => $request->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
            'synopsis_report_doc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'recommendation' => 'required|boolean',
            'main_supervisor_student_comment' => 'nullable|string',
            'main_supervisor_confidential_remark' => $request->boolean('recommendation') ? 'nullable|string' : 'required|string',
        ]);

        // Optional Synopsis Replacement by Main Supervisor
        $filePath = $pts2->synopsis_report_doc_path;
        if ($request->hasFile('synopsis_report_doc')) {
            $filePath = $request->file('synopsis_report_doc')->store('private/pts2_documents', 'local');
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
            'synopsis_report_doc_path' => $filePath,
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
    public function showReview(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        $isSupervisorOrFaculty = $user->isFaculty();
        $isAuthority = ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)));

        if (!$isSupervisorOrFaculty && !$isAuthority) {
            abort(403, 'Unauthorized access to review this submission.');
        }

        $mainSupervisor = $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $coSupervisors[$i] = User::find($pts2->$col);
            }
        }

        $academicOffice = $user->isAcademicOffice();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_active', true)->with('user')->get()->pluck('user')->filter();

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
    public function endorse(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $stage = $pts2->current_stage;

        if ($stage === 'academic_office') {
            if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                return back()->with('error', 'Unauthorized access.');
            }

            $validated = $request->validate([
                'verified_details' => 'required|accepted',
                'verification_remark' => 'required|string',
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
                'student_comment' => 'nullable|string',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string' : 'required|string',
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
                'student_comment' => 'nullable|string',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string' : 'required|string',
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
                        'approved_by_authority' => $user->email,
                        'current_stage' => 'completed',
                        'status' => $isRecommended ? 'approved' : 'rejected',
                    ]);
                    break;

                default:
                    return back()->with('error', 'Invalid stage for endorsement.');
            }
        }

        return redirect()->route('dashboard')->with('success', 'PTS-2 form evaluated and submitted successfully!');
    }

    // Universal Pop-up Reversion action for all evaluating authorities.
    public function revert(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;

        if ($user->isAcademicOffice() || $pts2->current_stage === 'academic_office') {
            return back()->with('error', 'Academic Office cannot revert forms; verification and forwarding only.');
        }

        $validated = $request->validate([
            'reversion_comment' => 'required|string|min:5|max:2000',
        ]);

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

        return redirect()->route('dashboard')->with('warning', 'PTS-2 Synopsis Form has been reverted back to the student.');
    }

    // Show read-only details of an approved/rejected/in-progress PTS-2 form.
    public function show(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        $isOwnerStudent = ($user->isStudent() && $student->user_id === $user->id);
        $isSupervisorOrFaculty = $user->isFaculty();
        $isAuthority = ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)));

        if (!$isOwnerStudent && !$isSupervisorOrFaculty && !$isAuthority) {
            abort(403, 'Unauthorized access to view this submission.');
        }

        $mainSupervisor = $student->mainSupervisors->first();

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

        if (!$pts2->canUserViewRevertedForm($user)) {
            abort(403, 'Unauthorized access to view this reverted PTS-2 form.');
        }

        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        $mainSupervisor = $student->mainSupervisors->first();

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
}
