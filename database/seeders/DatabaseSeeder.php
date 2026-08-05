<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\Pts1Form;
use App\Models\Pts2Form;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1. Base Department
        $dept = Department::firstOrCreate(
            ['code' => 'CSE'],
            ['name' => 'Computer Science and Engineering', 'is_active' => true]
        );

        // 2. Separate Admin Model Accounts (System Admin & Department Super Admin)
        Admin::firstOrCreate(
            ['email' => 'systemadmin@iiti.ac.in'],
            ['name' => 'System Admin', 'password' => $password, 'admin_type' => 'system_admin', 'is_active' => true]
        );

        Admin::firstOrCreate(
            ['email' => 'superadmin@iiti.ac.in'],
            ['name' => 'Department Super Admin', 'password' => $password, 'admin_type' => 'super_admin', 'department_id' => $dept->id, 'is_active' => true]
        );

        // 3. Global Authorities (Portal Users)
        User::firstOrCreate(
            ['email' => 'doaa@iiti.ac.in'],
            ['name' => 'Dean of Academic Affairs', 'password' => $password, 'role' => 'doaa', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'adoaa@iiti.ac.in'],
            ['name' => 'Associate Dean', 'password' => $password, 'role' => 'adoaa', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'senatechairperson@iiti.ac.in'],
            ['name' => 'Senate Chairperson', 'password' => $password, 'role' => 'senate_chairperson', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'aracademic@iiti.ac.in'],
            ['name' => 'Assistant Registrar (Academic)', 'password' => $password, 'role' => 'ar_academic', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'sectionofficer@iiti.ac.in'],
            ['name' => 'Section Officer', 'password' => $password, 'role' => 'section_officer', 'is_active' => true]
        );

        // 4. Department Authorities
        $hodUser = User::firstOrCreate(
            ['email' => 'hod@iiti.ac.in'],
            ['name' => 'Head of Department', 'password' => $password, 'role' => 'hod', 'is_active' => true]
        );
        $hodUser->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $dpgcUser = User::firstOrCreate(
            ['email' => 'dpgc@iiti.ac.in'],
            ['name' => 'DPGC Member', 'password' => $password, 'role' => 'dpgc', 'is_active' => true]
        );
        $dpgcUser->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        // 5. Faculty Members
        $faculty1 = User::firstOrCreate(
            ['email' => 'mainsupervisor@iiti.ac.in'],
            ['name' => 'Dr. Main Supervisor', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty1->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty2 = User::firstOrCreate(
            ['email' => 'cosupervisor@iiti.ac.in'],
            ['name' => 'Dr. Co-Supervisor', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty2->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty3 = User::firstOrCreate(
            ['email' => 'externalsupervisor@iiti.ac.in'],
            ['name' => 'Dr. External Supervisor', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty3->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        // 6. PhD Scholar (With existing thesis & PTS-1/PTS-2)
        $studentUser = User::firstOrCreate(
            ['email' => 'phdstudent@iiti.ac.in'],
            ['name' => 'Test PhD Scholar', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'roll_number' => '230001001',
                'department_id' => $dept->id,
                'date_joining' => '2023-08-01',
                'date_registration' => '2023-08-15',
                'date_confirmation' => '2024-08-01',
            ]
        );

        $thesis = Thesis::firstOrCreate(
            ['student_id' => $student->id],
            [
                'title' => 'Deep Learning Architectures for Academic Systems',
                'current_status' => 'In Progress',
            ]
        );

        if (!$thesis->supervisors()->where('faculty_user_id', $faculty1->id)->exists()) {
            $thesis->supervisors()->attach($faculty1->id, ['supervisor_type' => 'main']);
        }

        if (!$thesis->supervisors()->where('faculty_user_id', $faculty2->id)->exists()) {
            $thesis->supervisors()->attach($faculty2->id, ['supervisor_type' => 'co']);
        }

        if (!$thesis->pspcMembers()->where('faculty_user_id', $faculty2->id)->exists()) {
            $thesis->pspcMembers()->attach($faculty2->id);
        }

        if (!$thesis->pspcMembers()->where('faculty_user_id', $faculty3->id)->exists()) {
            $thesis->pspcMembers()->attach($faculty3->id);
        }

        // // 7. Seed Sample PTS-1 Form
        // Pts1Form::firstOrCreate(
        //     ['thesis_id' => $thesis->id],
        //     [
        //         'seminar_date' => '2026-08-15',
        //         'seminar_time' => '10:30 AM',
        //         'seminar_venue' => 'Seminar Hall 1, CSE Dept',
        //         'meeting_link' => 'https://meet.google.com/abc-defg-hij',
        //         'publication_norm_fulfillment' => true,
        //         'special_approval_publication' => false,
        //         'min_time_req_fulfilled' => true,
        //         'special_approval_min_time' => false,
        //         'draft_synopsis_report_doc_path' => 'documents/draft_synopsis_report.pdf',
        //         'publication_list_doc_path' => 'documents/publication_list.pdf',
        //         'work_status' => 'adequate',
        //         'additional_comments' => 'Seminar completed successfully with adequate research progress.',
        //         'current_stage' => 'main_supervisor',
        //         'status' => 'in_progress',
        //         'co_supervisor_1_id' => $faculty2->id,
        //         'pspc_member_1_id' => $faculty2->id,
        //         'pspc_member_2_id' => $faculty3->id,
        //     ]
        // );

        // // 8. Seed Sample PTS-2 Form
        // Pts2Form::firstOrCreate(
        //     ['thesis_id' => $thesis->id],
        //     [
        //         'synopsis_report_doc_path' => 'documents/final_synopsis_report.pdf',
        //         'current_stage' => 'main_supervisor',
        //         'status' => 'in_progress',
        //         'co_supervisor_1_id' => $faculty2->id,
        //         'pspc_member_1_id' => $faculty2->id,
        //         'pspc_member_2_id' => $faculty3->id,
        //     ]
        // );

        // 9. NEW PhD Scholar (NO Thesis Submitted Yet)
        $newStudentUser = User::firstOrCreate(
            ['email' => 'newstudent@iiti.ac.in'],
            ['name' => 'Fresh PhD Scholar', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        Student::firstOrCreate(
            ['user_id' => $newStudentUser->id],
            [
                'roll_number' => '240041001',
                'department_id' => $dept->id,
                'date_joining' => '2024-08-01',
                'date_registration' => '2024-08-15',
                'date_confirmation' => '2025-08-01',
            ]
        );

        $this->command->info('Successfully seeded database with PTS-1 and PTS-2 Form architectures and Fresh Scholar!');
    }
}