<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pts1Form;
use App\Models\Pts2Form;
use App\Models\Pts2Extension;
use App\Models\Thesis;

echo "=== Verifying Status & Terminal State Contract ===\n";

$student = \App\Models\Student::first();
if (!$student) {
    echo "No student found in database.\n";
    exit(1);
}

$thesis = Thesis::firstOrCreate(
    ['student_id' => $student->id],
    [
        'title' => 'Sample Research Thesis',
        'status' => 'in_progress',
    ]
);

// 1. PTS-1 Test
$pts1 = Pts1Form::create([
    'thesis_id' => $thesis->id,
    'thesis_title' => 'Test Thesis PTS-1',
    'seminar_date' => now()->toDateString(),
    'seminar_time' => '10:00:00',
    'seminar_venue' => 'Seminar Hall 1',
    'draft_synopsis_report_doc_path' => 'dummy.pdf',
    'publication_list_doc_path' => 'dummy.xlsx',
    'publication_norm_fulfillment' => true,
    'min_time_req_fulfilled' => true,
    'status' => 'in_progress',
    'current_stage' => 'main_supervisor',
]);

echo "Created PTS-1: status={$pts1->status}, current_stage={$pts1->current_stage}\n";
assert($pts1->status === 'in_progress' && $pts1->current_stage === 'main_supervisor');

// DOAA Accept
$pts1->update([
    'status' => 'approved',
    'current_stage' => 'completed',
]);
echo "approved PTS-1: status={$pts1->status}, current_stage={$pts1->current_stage}\n";
assert($pts1->status === 'approved' && $pts1->current_stage === 'completed');

// DOAA Reject
$pts1->update([
    'status' => 'rejected',
    'current_stage' => 'completed',
]);
echo "Rejected PTS-1: status={$pts1->status}, current_stage={$pts1->current_stage}\n";
assert($pts1->status === 'rejected' && $pts1->current_stage === 'completed');

// Revert
$pts1->update([
    'status' => 'reverted',
    'current_stage' => 'reverted',
]);
echo "Reverted PTS-1: status={$pts1->status}, current_stage={$pts1->current_stage}\n";
assert($pts1->status === 'reverted' && $pts1->current_stage === 'reverted');

// 2. PTS-2 Test
$pts2 = Pts2Form::create([
    'thesis_id' => $thesis->id,
    'thesis_title' => 'Test Thesis PTS-2',
    'synopsis_report_doc_path' => 'dummy.pdf',
    'course_credits_student' => 50,
    'current_address' => 'Test Address',
    'recent_phone_number' => '9999999999',
    'cert_prima_facie_case' => true,
    'cert_no_prior_degree_submission' => true,
    'status' => 'in_progress',
    'current_stage' => 'main_supervisor',
]);
echo "Created PTS-2: status={$pts2->status}, current_stage={$pts2->current_stage}\n";
assert($pts2->status === 'in_progress' && $pts2->current_stage === 'main_supervisor');

// DOAA Accept
$pts2->update([
    'status' => 'approved',
    'current_stage' => 'completed',
]);
echo "approved PTS-2: status={$pts2->status}, current_stage={$pts2->current_stage}\n";
assert($pts2->status === 'approved' && $pts2->current_stage === 'completed');

// DOAA Reject
$pts2->update([
    'status' => 'rejected',
    'current_stage' => 'completed',
]);
echo "Rejected PTS-2: status={$pts2->status}, current_stage={$pts2->current_stage}\n";
assert($pts2->status === 'rejected' && $pts2->current_stage === 'completed');

// Revert
$pts2->update([
    'status' => 'reverted',
    'current_stage' => 'reverted',
]);
echo "Reverted PTS-2: status={$pts2->status}, current_stage={$pts2->current_stage}\n";
assert($pts2->status === 'reverted' && $pts2->current_stage === 'reverted');

// 3. PTS-2 Extension Test
$ext = Pts2Extension::create([
    'thesis_id' => $thesis->id,
    'reason_for_extension' => 'Need additional experiments',
    'extended_until_date' => now()->addDays(20)->toDateString(),
    'status' => 'in_progress',
    'current_stage' => 'main_supervisor',
]);
echo "Created PTS-2 Extension: status={$ext->status}, current_stage={$ext->current_stage}\n";
assert($ext->status === 'in_progress' && $ext->current_stage === 'main_supervisor');

// DOAA Accept
$ext->update([
    'status' => 'approved',
    'current_stage' => 'completed',
]);
echo "approved PTS-2 Extension: status={$ext->status}, current_stage={$ext->current_stage}\n";
assert($ext->status === 'approved' && $ext->current_stage === 'completed');

// DOAA Reject
$ext->update([
    'status' => 'rejected',
    'current_stage' => 'completed',
]);
echo "Rejected PTS-2 Extension: status={$ext->status}, current_stage={$ext->current_stage}\n";
assert($ext->status === 'rejected' && $ext->current_stage === 'completed');

// Revert
$ext->update([
    'status' => 'reverted',
    'current_stage' => 'reverted',
]);
echo "Reverted PTS-2 Extension: status={$ext->status}, current_stage={$ext->current_stage}\n";
assert($ext->status === 'reverted' && $ext->current_stage === 'reverted');

echo "\nALL STATUS & CURRENT_STAGE CONTRACT TESTS PASSED SUCCESSFULLY!\n";
