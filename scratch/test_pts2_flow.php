<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Thesis;
use App\Models\Student;
use App\Models\Pts1Form;
use App\Models\Pts2Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

echo "=== STARTING COMPREHENSIVE PTS-2 WORKFLOW VERIFICATION ===\n";

$studentUser = User::where('email', 'phdstudent1@iiti.ac.in')->firstOrFail();
$student = $studentUser->student;
$thesis = Thesis::firstOrCreate(
    ['student_id' => $student->id],
    [
        'title' => 'Deep Learning on Edge Devices',
        'status' => 'in_progress',
        'open_seminar_date' => now()->subDays(5),
    ]
);

// Step 0: Ensure PTS-1 is approved so PTS-2 is unlocked
$pts1 = Pts1Form::firstOrCreate(
    ['thesis_id' => $thesis->id],
    [
        'seminar_date' => now()->subDays(10)->toDateString(),
        'seminar_time' => '10:00:00',
        'seminar_venue' => 'Room 101',
        'presentation_mode' => 'offline',
        'draft_synopsis_report_doc_path' => 'dummy/path.pdf',
        'publication_list_doc_path' => 'dummy/pub.pdf',
        'status' => 'approved',
        'current_stage' => 'completed',
        'doaa_approval' => true,
        'doaa_submitted_at' => now(),
    ]
);
$pts1->update(['status' => 'approved', 'current_stage' => 'completed', 'doaa_approval' => true]);

// Clean previous test PTS-2 forms
Pts2Form::where('thesis_id', $thesis->id)->delete();

// Step 1: Student submits PTS-2
echo "\n--- Step 1: Student Submits PTS-2 ---\n";
Auth::login($studentUser);

$pts2 = Pts2Form::create([
    'thesis_id' => $thesis->id,
    'thesis_title' => 'Test Synopsis for PhD Thesis',
    'synopsis_report_doc_path' => 'dummy/synopsis.pdf',
    'current_stage' => 'main_supervisor',
    'status' => 'in_progress',
    'date_of_submission' => now()->toDateString(),
    'course_credits_student' => 36.0,
    'current_address' => 'Campus Hostel, IIT Indore',
    'recent_phone_number' => '9876543210',
    'recent_phone_country_code' => '+91',
    'recent_phone_iso2' => 'in',
    'alternate_phone_number' => '9123456789',
    'alternate_phone_country_code' => '+1',
    'alternate_phone_iso2' => 'us',
    'cert_prima_facie_case' => true,
    'cert_no_prior_degree_submission' => true,
    'collaborative_work_status' => false,
    'co_supervisor_1_id' => User::where('email', 'cosupervisor1@iiti.ac.in')->first()->id,
]);

echo "Created PTS-2 Form ID: {$pts2->id}, Status: {$pts2->status}, Stage: {$pts2->current_stage}\n";
assert($pts2->status === 'in_progress');
assert($pts2->current_stage === 'main_supervisor');
assert($pts2->getFormattedRecentPhoneNumber() === '+91 9876543210');
assert($pts2->getFormattedAlternatePhoneNumber() === '+1 9123456789');

// Step 2: Main Supervisor evaluates & forwards
echo "\n--- Step 2: Main Supervisor Evaluates ---\n";
$mainSupUser = User::where('email', 'mainsupervisor@iiti.ac.in')->firstOrFail();
Auth::login($mainSupUser);

$pts2->update([
    'main_supervisor_cert_prima_facie_case' => true,
    'main_supervisor_cert_no_prior_degree_submission' => true,
    'main_supervisor_collaborative_work_status' => false,
    'main_supervisor_recommendation' => true,
    'main_supervisor_student_comment' => 'Excellent progress on synopsis.',
    'main_supervisor_confidential_remark' => 'Strong candidate.',
    'main_supervisor_submitted_at' => now(),
    'current_stage' => 'co_supervisors',
]);

echo "After Main Sup: Status: {$pts2->status}, Stage: {$pts2->current_stage}\n";
assert($pts2->current_stage === 'co_supervisors');

// Step 3: Co-Supervisor evaluates & forwards
echo "\n--- Step 3: Co-Supervisor Evaluates ---\n";
$coSupUser = User::where('email', 'cosupervisor1@iiti.ac.in')->firstOrFail();
Auth::login($coSupUser);

$pts2->update([
    'co_supervisor_1_recommendation' => true,
    'co_supervisor_1_student_comment' => 'Good synopsis draft.',
    'co_supervisor_1_confidential_remark' => 'Recommended.',
    'co_supervisor_1_submitted_at' => now(),
    'co_supervisors_submitted_at' => now(),
    'current_stage' => 'academic_office',
]);

echo "After Co-Sup: Status: {$pts2->status}, Stage: {$pts2->current_stage}\n";
assert($pts2->current_stage === 'academic_office');

// Step 4: Academic Office verifies and updates credits
echo "\n--- Step 4: Academic Office Evaluates ---\n";
$aoUser = User::where('email', 'academicoffice@iiti.ac.in')->firstOrFail();
Auth::login($aoUser);

assert($aoUser->isAcademicOffice() === true);
assert($aoUser->isGlobalAuthority() === true);

$pts2->update([
    'academic_office_is_verified' => true,
    'academic_office_verification_remark' => 'Verified all courses, fee receipts, and seminar date.',
    'academic_office_course_credits' => 38.0,
    'academic_office_submitted_at' => now(),
    'current_stage' => 'doaa',
]);

$student->update(['course_credits_earned' => 38.0]);

echo "After Academic Office: Status: {$pts2->status}, Stage: {$pts2->current_stage}\n";
assert($pts2->current_stage === 'doaa');
assert($student->fresh()->course_credits_earned == 38.0);

// Step 5: DOAA Approves
echo "\n--- Step 5: DOAA Approves ---\n";
$doaaUser = User::where('email', 'doaa@iiti.ac.in')->firstOrFail();
Auth::login($doaaUser);

$pts2->update([
    'doaa_approval' => true,
    'doaa_student_comment' => 'Approved for synopsis presentation.',
    'doaa_confidential_remark' => 'Proceed with examiners nomination.',
    'doaa_submitted_at' => now(),
    'current_stage' => 'completed',
    'status' => 'approved',
]);

echo "After DOAA Approval: Status: {$pts2->status}, Stage: {$pts2->current_stage}\n";
assert($pts2->status === 'approved');
assert($pts2->current_stage === 'completed');

// Step 6: Test Reversion Lifecycle
echo "\n--- Step 6: Test Reversion & Role Rank ---\n";
$pts2Revert = Pts2Form::create([
    'thesis_id' => $thesis->id,
    'thesis_title' => 'Test Reversion Synopsis',
    'synopsis_report_doc_path' => 'dummy/synopsis.pdf',
    'current_stage' => 'main_supervisor',
    'status' => 'in_progress',
    'date_of_submission' => now()->toDateString(),
    'course_credits_student' => 36.0,
    'current_address' => 'Campus Hostel',
    'recent_phone_number' => '9876543210',
    'cert_prima_facie_case' => true,
    'cert_no_prior_degree_submission' => true,
    'collaborative_work_status' => false,
]);

// Main Supervisor Reverts
Auth::login($mainSupUser);
$pts2Revert->update([
    'status' => 'reverted',
    'current_stage' => 'reverted',
    'reverted_by_role' => 'main_supervisor',
    'reverted_by_id' => $mainSupUser->id,
    'reversion_comment' => 'Please revise the synopsis abstract and resubmit.',
]);

echo "Reverted Form: Status: {$pts2Revert->status}, Stage: {$pts2Revert->current_stage}\n";
assert($pts2Revert->status === 'reverted');
assert($pts2Revert->current_stage === 'reverted');

// Check visibility permissions
assert($pts2Revert->canUserViewRevertedForm($studentUser) === true);
assert($pts2Revert->canUserViewRevertedForm($mainSupUser) === true);
assert($pts2Revert->canUserViewRevertedForm($aoUser) === false); // AO rank (3) > Main Sup rank (1)
assert($pts2Revert->canUserViewRevertedForm($doaaUser) === false); // DOAA rank (4) > Main Sup rank (1)

// Step 7: Test Blade View Renderings
echo "\n--- Step 7: Test Blade View Renderings ---\n";
Auth::login($aoUser);

View::share('errors', new \Illuminate\Support\ViewErrorBag());

$pts2Form = $pts2;
$viewShow = View::make('pts2.show', [
    'pts2' => $pts2,
    'thesis' => $thesis,
    'student' => $student,
    'studentUser' => $studentUser,
    'mainSupervisor' => $mainSupUser,
    'coSupervisors' => [1 => $coSupUser],
])->render();
echo "Rendered pts2.show: " . strlen($viewShow) . " bytes\n";
assert(strpos($viewShow, 'Academic Office') !== false);

$viewReview = View::make('pts2.review_endorse', [
    'pts2' => $pts2,
    'thesis' => $thesis,
    'student' => $student,
    'studentUser' => $studentUser,
    'mainSupervisor' => $mainSupUser,
    'coSupervisors' => [1 => $coSupUser],
    'academicOffice' => true,
])->render();
echo "Rendered pts2.review_endorse: " . strlen($viewReview) . " bytes\n";
assert(strpos($viewReview, 'Academic Office') !== false);

$pts2Pending = Pts2Form::create([
    'thesis_id' => $thesis->id,
    'thesis_title' => 'Pending Synopsis for Dashboard Test',
    'synopsis_report_doc_path' => 'dummy/synopsis.pdf',
    'current_stage' => 'academic_office',
    'status' => 'in_progress',
    'date_of_submission' => now()->toDateString(),
    'course_credits_student' => 36.0,
    'current_address' => 'Campus Hostel',
    'recent_phone_number' => '9876543210',
    'cert_prima_facie_case' => true,
    'cert_no_prior_degree_submission' => true,
    'collaborative_work_status' => false,
]);

$viewDashboard = View::make('global_authorities.dashboard', [
    'user' => $aoUser,
    'phdStudents' => collect([$student]),
    'msrStudents' => collect([]),
    'departments' => \App\Models\Department::all(),
])->render();
echo "Rendered global_authorities.dashboard: " . strlen($viewDashboard) . " bytes\n";
assert(strpos($viewDashboard, 'Review & Endorse PTS-2 Form') !== false);

// Clean up test forms
$pts2Pending->delete();
$pts2Revert->delete();

echo "\n✅ ALL PTS-2 FLOW & VIEW TESTS PASSED CLEANLY!\n";
