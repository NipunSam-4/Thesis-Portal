<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts2Form;
use App\Http\Controllers\Pts2Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

echo "--- Verifying PTS-2 Declarations Pre-population and Main Supervisor Persistence ---\n";

DB::beginTransaction();
try {
    $dept = \App\Models\Department::firstOrCreate(
        ['code' => 'TEST_DEPT_PTS2_CERT'],
        ['name' => 'Department of Cert Test']
    );

    $studentUser = User::firstOrCreate(
        ['email' => 'student_cert@test.com'],
        ['name' => 'Student Cert', 'password' => bcrypt('password'), 'role' => 'student']
    );

    $studentId = DB::table('students')->insertGetId([
        'user_id' => $studentUser->id,
        'department_id' => $dept->id,
        'roll_number' => '2026TESTPTS2CERT',
        'program_name' => 'phd',
        'course_credits_earned' => 45,
        'date_registration' => '2022-08-01',
        'date_joining' => '2022-08-01',
        'date_confirmation' => '2023-08-01',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $student = Student::find($studentId);

    $mainSupervisor = User::firstOrCreate(
        ['email' => 'main_sup_cert@test.com'],
        ['name' => 'Main Supervisor Cert', 'password' => bcrypt('password'), 'role' => 'faculty']
    );

    $thesis = Thesis::create([
        'student_id' => $student->id,
        'title' => 'Declarations Test Thesis',
        'status' => 'in_progress',
    ]);

    DB::table('student_supervisor')->updateOrInsert(
        ['student_id' => $student->id, 'faculty_user_id' => $mainSupervisor->id],
        ['supervisor_type' => 'main']
    );

    // Student creates PTS-2
    $pts2 = Pts2Form::create([
        'thesis_id' => $thesis->id,
        'thesis_title' => $thesis->title,
        'synopsis_report_doc_path' => 'dummy/synopsis.pdf',
        'course_credits_student' => 45,
        'date_of_submission' => now()->toDateString(),
        'current_address' => 'Test Address',
        'recent_phone_number' => '9876543210',
        'cert_prima_facie_case' => true,
        'cert_no_prior_degree_submission' => true,
        'collaborative_work_status' => true,
        'collaborative_work_details' => 'Student initial collaborative work details',
        'status' => 'pending',
        'current_stage' => 'main_supervisor',
    ]);

    // Before Main Supervisor submits: effective values fall back to student's
    assert($pts2->getEffectiveCertPrimaFacieCase() === true, "Pre-endorse prima facie check");
    assert($pts2->getEffectiveCertNoPriorDegreeSubmission() === true, "Pre-endorse no prior degree check");
    assert($pts2->getEffectiveCollaborativeWorkStatus() === true, "Pre-endorse collab status check");
    assert($pts2->getEffectiveCollaborativeWorkDetails() === 'Student initial collaborative work details', "Pre-endorse collab details check");
    echo "✓ Pre-endorsement effective values fallback to student values verified.\n";

    // Main Supervisor endorses with modified collab values
    auth()->login($mainSupervisor);
    $req = Request::create("/pts2/{$pts2->id}/endorse", 'POST', [
        'cert_prima_facie_case' => '1',
        'cert_no_prior_degree_submission' => '1',
        'collaborative_work_status' => '0',
        'collaborative_work_details' => null,
        'recommendation' => '1',
        'student_comment' => 'Great work on synopsis!',
        'confidential_remark' => 'Supervisor confidential note',
    ]);

    $controller = app(Pts2Controller::class);
    $controller->endorse($req, $pts2);
    $pts2->refresh();

    // Verify Main Supervisor columns are populated
    assert($pts2->main_supervisor_cert_prima_facie_case === true, "Main sup prima facie stored");
    assert($pts2->main_supervisor_cert_no_prior_degree_submission === true, "Main sup no prior degree stored");
    assert($pts2->main_supervisor_collaborative_work_status === false, "Main sup collab status stored");
    assert($pts2->main_supervisor_collaborative_work_details === null, "Main sup collab details stored");
    echo "✓ Main Supervisor database columns populated successfully.\n";

    // Verify effective values now reflect Main Supervisor columns
    assert($pts2->getEffectiveCertPrimaFacieCase() === true, "Post-endorse prima facie check");
    assert($pts2->getEffectiveCertNoPriorDegreeSubmission() === true, "Post-endorse no prior degree check");
    assert($pts2->getEffectiveCollaborativeWorkStatus() === false, "Post-endorse collab status check (supervisor overrode to false)");
    assert($pts2->getEffectiveCollaborativeWorkDetails() === null, "Post-endorse collab details check");
    echo "✓ Post-endorsement display now exclusively uses Main Supervisor values.\n";

    echo "=== PTS-2 DECLARATIONS VERIFICATION PASSED ===\n";
} finally {
    DB::rollBack();
    echo "✓ Database transaction cleanly rolled back.\n";
}
