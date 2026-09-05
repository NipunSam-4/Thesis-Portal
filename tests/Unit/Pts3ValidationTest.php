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
use App\Models\Pts3OebMember;
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

        $thesis = new Thesis(['title' => 'Quantum Machine Learning Architectures']);
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
            'has_consent' => true,
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
            'has_consent' => true,
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
            'has_consent' => true,
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
            'has_consent' => true,
        ]);
        $intl2->id = 4;

        $oeb1 = new Pts3OebMember([
            'pts3_form_id' => $pts3->id,
            'name' => 'Dr. Internal Member 1',
            'designation' => 'Professor',
            'department' => 'CSE',
            'email' => 'oeb1@iiti.ac.in',
            'phone_number' => '9123456780',
        ]);
        $oeb1->id = 5;

        $pts3->setRelation('indianExaminers', collect([$indian1, $indian2]));
        $pts3->setRelation('internationalExaminers', collect([$intl1, $intl2]));
        $pts3->setRelation('oebMembers', collect([$oeb1]));

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
        $indianExaminers = $pts3->indianExaminers;
        $internationalExaminers = $pts3->internationalExaminers;
        $oebMembers = $pts3->oebMembers;

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
        $indianExaminers = $pts3->indianExaminers;
        $internationalExaminers = $pts3->internationalExaminers;
        $oebMembers = $pts3->oebMembers;

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
        $indianExaminers = $pts3->indianExaminers;
        $internationalExaminers = $pts3->internationalExaminers;
        $oebMembers = $pts3->oebMembers;

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
        $indianExaminers = $pts3->indianExaminers;
        $internationalExaminers = $pts3->internationalExaminers;
        $oebMembers = $pts3->oebMembers;

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
        $indianExaminers = $pts3->indianExaminers;
        $internationalExaminers = $pts3->internationalExaminers;
        $oebMembers = $pts3->oebMembers;

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
}
