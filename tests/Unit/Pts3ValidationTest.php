<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use App\Models\Thesis;
use App\Models\Pts1Form;
use App\Models\Pts3Form;
use App\Models\Pts3Examiner;
use App\Models\Pts3OebChairperson;
use App\Http\Requests\Pts3\StorePts3Request;
use Illuminate\Support\Facades\Validator;

class Pts3ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        view()->share('errors', new \Illuminate\Support\ViewErrorBag);
    }

    private function createMockStudentAndThesis(): array
    {
        $dept = new Department(['name' => 'Computer Science & Engineering', 'code' => 'CSE']);
        $facultyUser = new User(['name' => 'Prof. Supervisor', 'email' => 'fac@iiti.ac.in', 'role' => 'faculty']);
        $facultyUser->id = 1;

        $studentUser = new User(['name' => 'John Doe', 'email' => 'john@iiti.ac.in', 'role' => 'student']);
        $studentUser->id = 2;

        $student = new Student([
            'roll_number' => 'PhD2301001',
            'program_name' => 'phd',
            'date_registration' => '2023-08-01',
            'date_joining' => '2023-08-01',
            'date_confirmation' => '2024-08-01',
        ]);
        $student->id = 101;
        $student->setRelation('user', $studentUser);
        $student->setRelation('department', $dept);
        $student->setRelation('mainSupervisors', collect([$facultyUser]));
        $student->setRelation('coSupervisors', collect([]));
        $student->setRelation('externalSupervisors', collect([]));

        $thesis = new Thesis(['title' => 'Quantum Machine Learning Architectures', 'status' => 'in_progress']);
        $thesis->id = 501;
        $thesis->setRelation('student', $student);
        $thesis->setRelation('pts1Form', new Pts1Form(['seminar_date' => '2023-09-15']));

        return [$facultyUser, $studentUser, $student, $thesis];
    }

    private function createMockPts3Form(Thesis $thesis, User $facultyUser, string $status = 'in_progress', string $stage = 'dpgc'): Pts3Form
    {
        $pts3 = new Pts3Form([
            'thesis_id' => $thesis->id,
            'thesis_title' => $thesis->title,
            'main_supervisor_id' => $facultyUser->id,
            'main_supervisor_recommendation' => true,
            'main_supervisor_submitted_at' => now(),
            'current_stage' => $stage,
            'status' => $status,
            'indian_examiner_1_email' => 'indian1@iitb.ac.in',
            'indian_examiner_1_has_consent' => true,
            'indian_examiner_2_email' => 'indian2@iitd.ac.in',
            'indian_examiner_2_has_consent' => true,
            'international_examiner_1_email' => 'intl1@mit.edu',
            'international_examiner_1_has_consent' => true,
            'international_examiner_2_email' => 'intl2@stanford.edu',
            'international_examiner_2_has_consent' => true,
            'oeb_chairperson_1_email' => 'oeb1@iiti.ac.in',
        ]);
        $pts3->id = 301;
        $pts3->setRelation('thesis', $thesis);
        $pts3->setRelation('mainSupervisor', $facultyUser);

        $indian1 = new Pts3Examiner([
            'pts3_form_id' => $pts3->id,
            'type' => 'indian',
            'name' => 'Dr. Indian One',
            'designation' => 'Professor',
            'organization' => 'IIT Bombay',
            'postal_address' => 'Mumbai, India',
            'email' => 'indian1@iitb.ac.in',
            'phone_number' => '9876543210',
            'phone_country_code' => '+91',
            'phone_iso2' => 'in',
        ]);
        $indian1->id = 1;

        $indian2 = new Pts3Examiner([
            'pts3_form_id' => $pts3->id,
            'type' => 'indian',
            'name' => 'Dr. Indian Two',
            'designation' => 'Associate Professor',
            'organization' => 'IIT Delhi',
            'postal_address' => 'New Delhi, India',
            'email' => 'indian2@iitd.ac.in',
            'phone_number' => '9876543211',
            'phone_country_code' => '+91',
            'phone_iso2' => 'in',
        ]);
        $indian2->id = 2;

        $intl1 = new Pts3Examiner([
            'pts3_form_id' => $pts3->id,
            'type' => 'international',
            'name' => 'Prof. International One',
            'designation' => 'Chair Professor',
            'organization' => 'MIT, USA',
            'postal_address' => 'Cambridge, MA, USA',
            'email' => 'intl1@mit.edu',
            'phone_number' => '1234567890',
            'phone_country_code' => '+1',
            'phone_iso2' => 'us',
        ]);
        $intl1->id = 3;

        $intl2 = new Pts3Examiner([
            'pts3_form_id' => $pts3->id,
            'type' => 'international',
            'name' => 'Prof. International Two',
            'designation' => 'Professor',
            'organization' => 'Stanford University, USA',
            'postal_address' => 'Stanford, CA, USA',
            'email' => 'intl2@stanford.edu',
            'phone_number' => '1234567891',
            'phone_country_code' => '+1',
            'phone_iso2' => 'us',
        ]);
        $intl2->id = 4;

        $oeb1 = new Pts3OebChairperson([
            'pts3_form_id' => $pts3->id,
            'name' => 'Dr. Internal Member 1',
            'designation' => 'Professor',
            'department' => 'CSE',
            'email' => 'oeb1@iiti.ac.in',
        ]);
        $oeb1->id = 5;

        $pts3->setRelation('examiners', collect([$indian1, $indian2, $intl1, $intl2]));
        $pts3->setRelation('oebChairpersons', collect([$oeb1]));

        return $pts3;
    }

    public function test_pts3_create_view_renders_cleanly(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();

        $html = view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser'))->render();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Submit PTS-3 Form', $html);
        $this->assertStringContainsString('Indian Examiners Panel', $html);
        $this->assertStringContainsString('International Examiners Panel', $html);
    }

    public function test_pts3_reverted_edit_view_renders_cleanly(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'reverted', 'main_supervisor');
        $pts3->reversion_comment = 'Please replace examiner 2 with someone in the exact domain.';
        $pts3->reverted_by_role = 'dpgc';

        $html = view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'pts3'))->render();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Resubmit PTS-3 Form', $html);
        $this->assertStringContainsString('Reverted by DPGC', $html);
        $this->assertStringContainsString('Please replace examiner 2', $html);
    }

    public function test_pts3_show_view_renders_approved(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'approved', 'completed');
        $pts3->senate_chairperson_approval = true;
        $pts3->senate_chairperson_submitted_at = now();
        $pts3->senate_chairperson_approval_remark = 'Examiner panel approved as proposed.';
        $userRank = 7;
        $coSupervisors = [];
        $indianExaminers = $pts3->getIndianExaminers();
        $internationalExaminers = $pts3->getInternationalExaminers();
        $oebMembers = $pts3->getOebChairpersons();

        $html = view('pts3.show', compact(
            'pts3', 'thesis', 'student', 'studentUser', 'user', 'userRank',
            'coSupervisors', 'indianExaminers', 'internationalExaminers', 'oebMembers'
        ))->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Approved PTS-3 Form', $html);
        $this->assertStringContainsString('Examiner panel approved as proposed.', $html);
    }

    public function test_pts3_show_view_renders_rejected(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'rejected', 'completed');
        $pts3->senate_chairperson_approval = false;
        $pts3->senate_chairperson_submitted_at = now();
        $pts3->senate_chairperson_approval_remark = 'Panel not meeting institutional diversity requirements.';
        $userRank = 7;
        $coSupervisors = [];
        $indianExaminers = $pts3->getIndianExaminers();
        $internationalExaminers = $pts3->getInternationalExaminers();
        $oebMembers = $pts3->getOebChairpersons();

        $html = view('pts3.show', compact(
            'pts3', 'thesis', 'student', 'studentUser', 'user', 'userRank',
            'coSupervisors', 'indianExaminers', 'internationalExaminers', 'oebMembers'
        ))->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Rejected PTS-3 Form', $html);
        $this->assertStringContainsString('Panel not meeting institutional diversity requirements.', $html);
    }

    public function test_pts3_show_view_renders_reverted(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'reverted', 'main_supervisor');
        $pts3->reverted_by_role = 'hod';
        $pts3->reversion_comment = 'Need more examiners from premier IITs.';
        $userRank = 1;
        $coSupervisors = [];
        $indianExaminers = $pts3->getIndianExaminers();
        $internationalExaminers = $pts3->getInternationalExaminers();
        $oebMembers = $pts3->getOebChairpersons();

        $html = view('pts3.show', compact(
            'pts3', 'thesis', 'student', 'studentUser', 'user', 'userRank',
            'coSupervisors', 'indianExaminers', 'internationalExaminers', 'oebMembers'
        ))->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Reverted PTS-3 Form', $html);
        $this->assertStringContainsString('Form Reverted by HOD', $html);
        $this->assertStringContainsString('Need more examiners from premier IITs.', $html);
    }

    public function test_pts3_review_view_renders_cleanly_for_dpgc(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'in_progress', 'dpgc');
        $userRank = 3;
        $coSupervisors = [];
        $actingDoaaUsers = collect([]);
        $indianExaminers = $pts3->getIndianExaminers();
        $internationalExaminers = $pts3->getInternationalExaminers();
        $oebMembers = $pts3->getOebChairpersons();

        $html = view('pts3.review', compact(
            'pts3', 'thesis', 'student', 'studentUser', 'user', 'userRank',
            'coSupervisors', 'actingDoaaUsers', 'indianExaminers', 'internationalExaminers', 'oebMembers'
        ))->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Panels of Examiners', $html);
        $this->assertStringContainsString('Revert Form', $html);
    }

    public function test_pts3_review_view_renders_cleanly_for_senate_chairperson(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $pts3 = $this->createMockPts3Form($thesis, $user, 'in_progress', 'senate_chairperson');
        $userRank = 7;
        $coSupervisors = [];
        $actingDoaaUsers = collect([]);
        $indianExaminers = $pts3->getIndianExaminers();
        $internationalExaminers = $pts3->getInternationalExaminers();
        $oebMembers = $pts3->getOebChairpersons();

        $html = view('pts3.review', compact(
            'pts3', 'thesis', 'student', 'studentUser', 'user', 'userRank',
            'coSupervisors', 'actingDoaaUsers', 'indianExaminers', 'internationalExaminers', 'oebMembers'
        ))->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Approval Status for Proposed Examiner Panels', $html);
        $this->assertStringContainsString('(a) APPROVE', $html);
        $this->assertStringContainsString('(b) DO NOT APPROVE', $html);
    }

    public function test_pts3_consent_combinations_validation(): void
    {
        $request = new StorePts3Request();
        
        // 1. Two Consented examiners (Valid)
        $valid2Consent = [
            ['has_consent' => '1'],
            ['has_consent' => '1'],
        ];
        $validator = Validator::make(['indian_examiners' => $valid2Consent], []);
        $request->withValidator($validator);
        $validator->passes();
        $this->assertFalse($validator->errors()->has('indian_examiners'));

        // 2. One Consent + Two Non-Consent examiners (Valid)
        $valid3Mixed = [
            ['has_consent' => '1'],
            ['has_consent' => '0'],
            ['has_consent' => '0'],
        ];
        $validator = Validator::make(['indian_examiners' => $valid3Mixed], []);
        $request->withValidator($validator);
        $validator->passes();
        $this->assertFalse($validator->errors()->has('indian_examiners'));

        // 3. Four Non-Consent examiners (Valid)
        $valid4NonConsent = [
            ['has_consent' => '0'],
            ['has_consent' => '0'],
            ['has_consent' => '0'],
            ['has_consent' => '0'],
        ];
        $validator = Validator::make(['indian_examiners' => $valid4NonConsent], []);
        $request->withValidator($validator);
        $validator->passes();
        $this->assertFalse($validator->errors()->has('indian_examiners'));

        // 4. Invalid: Two Non-Consent (only 1.0 unit)
        $invalid2NonConsent = [
            ['has_consent' => '0'],
            ['has_consent' => '0'],
        ];
        $validator = Validator::make(['indian_examiners' => $invalid2NonConsent], []);
        $request->withValidator($validator);
        $validator->passes();
        $this->assertTrue($validator->errors()->has('indian_examiners'));

        // 5. Invalid: Three examiners with Two Consents (2.5 units)
        $invalid3Mixed = [
            ['has_consent' => '1'],
            ['has_consent' => '1'],
            ['has_consent' => '0'],
        ];
        $validator = Validator::make(['indian_examiners' => $invalid3Mixed], []);
        $request->withValidator($validator);
        $validator->passes();
        $this->assertTrue($validator->errors()->has('indian_examiners'));
    }

    public function test_pts3_draft_view_renders_cleanly(): void
    {
        [$user, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $draft = new \App\Models\Pts3Draft([
            'thesis_id' => $thesis->id,
            'user_id' => $user->id,
            'thesis_title' => 'Draft Thesis Title for PTS-3',
            'indian_examiner_1_email' => 'draft.indian1@iitb.ac.in',
            'indian_examiner_1_has_consent' => true,
            'international_examiner_1_email' => 'draft.intl1@mit.edu',
            'international_examiner_1_has_consent' => true,
            'oeb_chairperson_1_email' => 'draft.oeb1@iiti.ac.in',
        ]);
        $draft->id = 701;

        $indianDraft1 = new \App\Models\Pts3ExaminerDraft([
            'pts3_draft_id' => $draft->id,
            'type' => 'indian',
            'name' => 'Draft Indian Examiner',
            'designation' => 'Professor',
            'organization' => 'IIT Bombay',
            'email' => 'draft.indian1@iitb.ac.in',
        ]);

        $intlDraft1 = new \App\Models\Pts3ExaminerDraft([
            'pts3_draft_id' => $draft->id,
            'type' => 'international',
            'name' => 'Draft International Examiner',
            'designation' => 'Chair Professor',
            'organization' => 'MIT',
            'email' => 'draft.intl1@mit.edu',
        ]);

        $oebDraft1 = new \App\Models\Pts3OebChairpersonDraft([
            'pts3_draft_id' => $draft->id,
            'name' => 'Draft OEB Chairperson',
            'designation' => 'Professor',
            'department' => 'CSE',
            'email' => 'draft.oeb1@iiti.ac.in',
        ]);

        $draft->setRelation('examinerDrafts', collect([$indianDraft1, $intlDraft1]));
        $draft->setRelation('oebChairpersonDrafts', collect([$oebDraft1]));

        $html = view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'draft'))->render();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('Submit PTS-3 Form', $html);
        $this->assertStringContainsString('Draft Thesis Title for PTS-3', $html);
        $this->assertStringContainsString('Draft Indian Examiner', $html);
        $this->assertStringContainsString('Draft International Examiner', $html);
        $this->assertStringContainsString('Draft OEB Chairperson', $html);
    }

    public function test_pts3_partial_draft_loading_with_slot(): void
    {
        [$facultyUser, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $user = $facultyUser;

        $draft = new \App\Models\Pts3Draft([
            'thesis_id' => $thesis->id,
            'user_id' => $user->id,
            'thesis_title' => 'Partial Draft Title',
        ]);
        $draft->id = 801;

        // Partial examiner 1 with email, examiner 2 with only name (no email), examiner 3 with only designation
        $indian1 = new \App\Models\Pts3ExaminerDraft([
            'pts3_draft_id' => $draft->id,
            'slot' => 1,
            'type' => 'indian',
            'name' => 'Dr. Full Examiner',
            'email' => 'full@iitb.ac.in',
        ]);
        $indian2 = new \App\Models\Pts3ExaminerDraft([
            'pts3_draft_id' => $draft->id,
            'slot' => 2,
            'type' => 'indian',
            'name' => 'Prof. Only Name',
            'email' => null,
        ]);
        $indian3 = new \App\Models\Pts3ExaminerDraft([
            'pts3_draft_id' => $draft->id,
            'slot' => 3,
            'type' => 'indian',
            'name' => null,
            'designation' => 'Associate Professor',
            'email' => null,
        ]);

        $oeb3 = new \App\Models\Pts3OebChairpersonDraft([
            'pts3_draft_id' => $draft->id,
            'slot' => 3,
            'name' => 'Dr. OEB Slot 3 Only Name',
            'email' => null,
        ]);

        $draft->setRelation('examinerDrafts', collect([$indian1, $indian2, $indian3]));
        $draft->setRelation('oebChairpersonDrafts', collect([$oeb3]));

        $indianList = $draft->getIndianExaminers();
        $this->assertCount(3, $indianList);
        $this->assertEquals('Prof. Only Name', $indianList[1]->name);
        $this->assertEquals('Associate Professor', $indianList[2]->designation);

        $oebList = $draft->getOebChairpersons();
        $this->assertCount(4, $oebList);
        $this->assertEquals('Dr. OEB Slot 3 Only Name', $oebList[2]->name);

        $html = view('faculty.pts3.create', compact('user', 'student', 'thesis', 'studentUser', 'draft'))->render();
        $this->assertStringContainsString('Prof. Only Name', $html);
        $this->assertStringContainsString('Dr. OEB Slot 3 Only Name', $html);
        $this->assertStringContainsString('OEB Chairperson #${index + 1}', $html);
        $this->assertStringContainsString('clearOebMember', $html);
        $this->assertStringContainsString('clearExaminer', $html);
    }

    public function test_parallel_pts2_and_pts3_progression_logic(): void
    {
        [$facultyUser, $studentUser, $student, $thesis] = $this->createMockStudentAndThesis();
        $student->setRelation('theses', collect([$thesis]));
        $thesis->setRelation('draftSynopsisCirculation', null);
        $thesis->setRelation('pts2Form', null);
        $thesis->setRelation('pts2Extension', null);
        $thesis->setRelation('pts3Form', null);
        $thesis->setRelation('pts4Form', null);
        $thesis->setRelation('pts4Extension', null);
        $thesis->setRelation('pts5Form', null);
        $thesis->setRelation('pts6Form', null);

        // 1. PTS-1 not approved yet -> Stage is PTS-1
        $thesis->pts1Form->status = 'in_progress';
        $this->assertEquals('PTS-1', $student->getThesisStageLabel());

        // 2. PTS-1 approved -> Both PTS-2 and PTS-3 open in parallel
        $thesis->pts1Form->status = 'approved';
        $this->assertEquals('PTS-2 & PTS-3', $student->getThesisStageLabel());

        // 3. PTS-2 approved while PTS-3 still in progress -> PTS-3 and PTS-4 open in parallel
        $pts2 = new \App\Models\Pts2Form(['status' => 'approved']);
        $thesis->setRelation('pts2Form', $pts2);
        $this->assertEquals('PTS-3 & PTS-4', $student->getThesisStageLabel());

        // 4. PTS-3 approved while PTS-4 still pending -> Only PTS-4 active
        $pts3 = new Pts3Form(['status' => 'approved']);
        $thesis->setRelation('pts3Form', $pts3);
        $this->assertEquals('PTS-4', $student->getThesisStageLabel());

        // 5. PTS-4 accepted and PTS-3 approved -> Opens PTS-5
        $pts4 = new \App\Models\Pts4Form(['status' => 'accepted']);
        $thesis->setRelation('pts4Form', $pts4);
        $this->assertEquals('PTS-5', $student->getThesisStageLabel());
    }

    public function test_validate_panel_priorities_logic(): void
    {
        $controller = new \App\Http\Controllers\Pts3Controller(app(\App\Services\PtsDocumentService::class));
        $refMethod = new \ReflectionMethod($controller, 'validatePanelPriorities');
        $refMethod->setAccessible(true);

        // 1. Valid: [1, 2, 0] with maxAllowed = 3
        $err = $refMethod->invoke($controller, [10 => 1, 20 => 2, 30 => 0], 'Indian Panel', 3);
        $this->assertNull($err);

        // 2. Valid: [0, 0] with maxAllowed = 2 (all unranked)
        $err = $refMethod->invoke($controller, [10 => 0, 20 => 0], 'Indian Panel', 2);
        $this->assertNull($err);

        // 3. Valid: [1, 0, 2] with maxAllowed = 3
        $err = $refMethod->invoke($controller, [10 => 1, 20 => 0, 30 => 2], 'International Panel', 3);
        $this->assertNull($err);

        // 4. Invalid: [1, 3, 0] with maxAllowed = 3 (gap: missing 2)
        $err = $refMethod->invoke($controller, [10 => 1, 20 => 3, 30 => 0], 'Indian Panel', 3);
        $this->assertNotNull($err);
        $this->assertStringContainsString('Positive priorities must be sequential starting from 1 without gaps', $err);

        // 5. Invalid: [1, 1, 0] (duplicate positive priority)
        $err = $refMethod->invoke($controller, [10 => 1, 20 => 1, 30 => 0], 'Indian Panel', 3);
        $this->assertNotNull($err);
        $this->assertStringContainsString('duplicate priority values', $err);

        // 6. Invalid: [3, 1] with maxAllowed = 2 (out of range)
        $err = $refMethod->invoke($controller, [10 => 3, 20 => 1], 'Indian Panel', 2);
        $this->assertNotNull($err);
        $this->assertStringContainsString('Only values from 0 to 2 are permitted', $err);

        // 7. Valid OEB: [1, 2, 3, 4] with maxAllowed = 4
        $err = $refMethod->invoke($controller, [1 => 1, 2 => 2, 3 => 3, 4 => 4], 'OEB Panel', 4);
        $this->assertNull($err);

        // 8. Valid OEB: [1, 2, 0, 0] with maxAllowed = 4
        $err = $refMethod->invoke($controller, [1 => 1, 2 => 2, 3 => 0, 4 => 0], 'OEB Panel', 4);
        $this->assertNull($err);
    }
}

