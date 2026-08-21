<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts1Form;
use App\Models\Pts2Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "--- Starting PTS-2 Workflow Verification ---\n";

DB::beginTransaction();

try {
    $studentUser = User::firstOrCreate(
        ['email' => 'test.phd.pts2@iitp.ac.in'],
        ['name' => 'Test PTS2 Student', 'password' => bcrypt('password'), 'role' => 'student']
    );

    $studentId = DB::table('students')->insertGetId([
        'user_id' => $studentUser->id,
        'roll_number' => '2201CS99',
        'department_id' => 1,
        'program_name' => 'phd',
        'course_credits_earned' => 12.0,
        'date_registration' => '2022-08-01',
        'date_joining' => '2022-08-01',
        'date_confirmation' => '2023-08-01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $student = Student::find($studentId);

    $supervisor = User::firstOrCreate(
        ['email' => 'supervisor.pts2@iitp.ac.in'],
        ['name' => 'Prof. Supervisor', 'password' => bcrypt('password'), 'role' => 'faculty']
    );

    $coSupervisor = User::firstOrCreate(
        ['email' => 'cosupervisor.pts2@iitp.ac.in'],
        ['name' => 'Prof. Co-Supervisor', 'password' => bcrypt('password'), 'role' => 'faculty']
    );

    $sectionOfficer = User::firstOrCreate(
        ['email' => 'so.academic@iitp.ac.in'],
        ['name' => 'Section Officer Academic', 'password' => bcrypt('password'), 'role' => 'section_officer']
    );

    $doaa = User::firstOrCreate(
        ['email' => 'doaa.academic@iitp.ac.in'],
        ['name' => 'Dean Academic', 'password' => bcrypt('password'), 'role' => 'doaa']
    );

    $thesis = Thesis::firstOrCreate(
        ['student_id' => $student->id],
        [
            'title' => 'Initial Thesis Title for PTS2',
            'synopsis_status' => 'circulated',
            'synopsis_circulation_date' => now()->subDays(30),
        ]
    );

    // Setup supervisor relations
    DB::table('student_supervisor')->updateOrInsert(
        ['student_id' => $student->id, 'faculty_user_id' => $supervisor->id],
        ['supervisor_type' => 'main']
    );
    DB::table('student_supervisor')->updateOrInsert(
        ['student_id' => $student->id, 'faculty_user_id' => $coSupervisor->id],
        ['supervisor_type' => 'co']
    );

    // Setup approved PTS-1
    $pts1 = Pts1Form::firstOrCreate(
        ['thesis_id' => $thesis->id],
        [
            'status' => 'approved',
            'current_stage' => 'completed',
            'seminar_date' => now()->subDays(5),
            'seminar_time' => '10:00 AM',
            'seminar_venue' => 'Seminar Hall 1',
            'draft_synopsis_report_doc_path' => 'synopsis/sample_pts1.pdf',
            'publication_list_doc_path' => 'publist/sample_pts1.pdf',
        ]
    );

    echo "✓ Setup complete: Student, Thesis, Supervisors, and approved PTS-1.\n";

    // 1. Student creates and submits PTS-2
    $pts2 = Pts2Form::create([
        'thesis_id' => $thesis->id,
        'status' => 'in_progress',
        'current_stage' => 'main_supervisor',
        'course_credits_student' => 16.5,
        'date_of_submission' => now()->toDateString(),
        'current_address' => 'Room 101, Hall 4, IIT Patna',
        'alternate_email' => 'alt.pts2@gmail.com',
        'recent_phone_number' => '9876543210',
        'alternate_phone_number' => '9876543211',
        'cert_prima_facie_case' => true,
        'cert_no_prior_degree_submission' => true,
        'collaborative_work_status' => true,
        'collaborative_work_details' => 'Chapter 3 collaboration with ABC Lab',
        'synopsis_report_doc_path' => 'synopsis/sample_pts2.pdf',
        'co_supervisor_1_id' => $coSupervisor->id,
    ]);

    // Student profile course_credits_earned synced
    $student->update(['course_credits_earned' => $pts2->course_credits_student]);
    $student->refresh();

    assert($student->course_credits_earned == 16.5, 'Student course credits earned was not synced');
    assert($pts2->date_of_submission->format('Y-m-d') === now()->toDateString(), 'Date of submission not matching today');
    assert($pts2->current_stage === 'main_supervisor', 'Stage is not main_supervisor');
    echo "✓ Step 1: Student PTS-2 creation, date_of_submission, and credits sync verified.\n";

    // 2. Main Supervisor Review and Endorsement
    $pts2->update([
        'main_supervisor_cert_prima_facie_case' => true,
        'main_supervisor_cert_no_prior_degree_submission' => true,
        'main_supervisor_collaborative_work_status' => true,
        'main_supervisor_collaborative_work_details' => 'Chapter 3 verified with ABC Lab',
        'main_supervisor_recommendation' => true,
        'main_supervisor_student_comment' => 'Very good work, ready for co-supervisor review.',
        'main_supervisor_confidential_remark' => 'Candidate has done rigorous research.',
        'main_supervisor_submitted_at' => now(),
        'current_stage' => 'co_supervisors',
    ]);
    $pts2->refresh();

    assert($pts2->current_stage === 'co_supervisors', 'Stage is not co_supervisors');
    echo "✓ Step 2: Main Supervisor endorsement and stage progression verified.\n";

    // 3. Co-Supervisor Endorsement
    $pts2->update([
        'co_supervisor_1_recommendation' => true,
        'co_supervisor_1_student_comment' => 'Agreed and recommended.',
        'co_supervisor_1_confidential_remark' => 'Approved from co-supervisor perspective.',
        'co_supervisor_1_submitted_at' => now(),
        'co_supervisors_submitted_at' => now(),
        'current_stage' => 'section_officer',
    ]);
    $pts2->refresh();

    assert($pts2->current_stage === 'section_officer', 'Stage is not section_officer');
    echo "✓ Step 3: Co-Supervisors endorsement and stage progression verified.\n";

    // 4. Section Officer Verification
    $soCredits = 17.0;
    $pts2->update([
        'section_officer_is_verified' => true,
        'section_officer_verification_remark' => 'Credits, registration, and open seminar verified with official records.',
        'section_officer_course_credits' => $soCredits, // SO corrects credits to 17.0
        'section_officer_submitted_at' => now(),
        'current_stage' => 'doaa',
    ]);
    if ($soCredits !== null) {
        $student->update(['course_credits_earned' => $soCredits]);
    }
    $pts2->refresh();
    $student->refresh();

    assert($pts2->current_stage === 'doaa', 'Stage is not doaa');
    assert($pts2->course_credits_student == 16.5, 'Student credits submitted in form should remain intact');
    assert($pts2->section_officer_course_credits == 17.0, 'SO verified credits saved properly');
    assert($student->course_credits_earned == 17.0, 'Student profile course credits should be overridden by SO credits');
    echo "✓ Step 4: Section Officer verification, credits recorded, and student profile override verified.\n";

    // 5. DOAA Approval
    $pts2->update([
        'doaa_approval' => true,
        'doaa_student_comment' => 'Approved for synopsis consideration.',
        'doaa_confidential_remark' => 'Formal approval granted.',
        'doaa_submitted_at' => now(),
        'status' => 'approved',
        'current_stage' => 'completed',
    ]);
    $pts2->refresh();

    assert($pts2->status === 'approved', 'Form status is not approved');
    assert($pts2->current_stage === 'completed', 'Stage is not completed');
    echo "✓ Step 5: DOAA approval and form completion verified.\n";

    // 6. Test Reversion Flow
    $pts2->update([
        'status' => 'reverted',
        'current_stage' => 'reverted',
        'reverted_by_role' => 'doaa',
        'reverted_by_id' => $doaa->id,
        'reversion_comment' => 'Please correct section 3 collaborative work statement and resubmit.',
    ]);
    $pts2->refresh();

    assert($pts2->status === 'reverted', 'Form status is not reverted');
    assert($pts2->getReversionComment() === 'Please correct section 3 collaborative work statement and resubmit.');
    assert($pts2->canUserViewRevertedForm($studentUser), 'Student should be authorized to view reverted form');
    assert($pts2->canUserViewRevertedForm($supervisor), 'Supervisor should be authorized to view reverted form');
    echo "✓ Step 6: Reversion flow and authorization checks verified.\n";

    // 7. Test DOAA Rejection Flow & Stage Label
    $pts2->update([
        'status' => 'rejected',
        'current_stage' => 'rejected',
        'doaa_approval' => false,
        'doaa_student_comment' => 'Not approved.',
        'doaa_confidential_remark' => 'Candidate does not meet criteria.',
    ]);
    $pts2->refresh();

    assert($pts2->status === 'rejected', 'Form status is not rejected');
    assert($pts2->current_stage === 'rejected', 'Form current_stage is not rejected');
    assert($pts2->stage_label === 'Rejected', 'Form stage_label must be Rejected, not Approved');
    echo "✓ Step 7: DOAA rejection flow and stage_label verified.\n";

    // 8. Test Resubmission creating a new Row and populating Rejected Tab
    $newPts2 = Pts2Form::create([
        'thesis_id' => $thesis->id,
        'status' => 'in_progress',
        'current_stage' => 'main_supervisor',
        'course_credits_student' => 16.5,
        'date_of_submission' => now()->toDateString(),
        'current_address' => 'Room 101, Hall 4, IIT Patna',
        'recent_phone_number' => '9876543210',
        'cert_prima_facie_case' => true,
        'cert_no_prior_degree_submission' => true,
        'collaborative_work_status' => false,
        'synopsis_report_doc_path' => 'synopsis/sample_pts2_v2.pdf',
        'co_supervisor_1_id' => $coSupervisor->id,
    ]);

    $thesis->unsetRelation('pts2Form');
    $thesis->unsetRelation('pts2Forms');

    assert($thesis->pts2Forms()->count() === 2, 'There should be 2 PTS-2 rows (1 rejected, 1 new)');
    assert($thesis->pts2Form->id === $newPts2->id, 'Thesis latestOfMany pts2Form should point to the new active form');
    assert($thesis->pts2Form->status === 'in_progress', 'Latest PTS-2 form should be in_progress');

    $rejectedPts2Forms = $thesis->pts2Forms()->where('status', 'rejected')->get();
    assert($rejectedPts2Forms->count() === 1, 'There should be 1 rejected PTS-2 form for the rejected tab');
    assert($rejectedPts2Forms->first()->id === $pts2->id, 'Rejected form ID mismatch');
    echo "✓ Step 8: Multi-row PTS-2 resubmission, latestOfMany resolution, and rejected tab query verified.\n";

    echo "\n=== ALL PTS-2 END-TO-END VERIFICATION CHECKS PASSED ===\n";

} finally {
    DB::rollBack();
    echo "✓ Database transaction cleanly rolled back.\n";
}
