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
    // Seed the application's database.
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1. Base Department
        $dept = Department::firstOrCreate(
            ['code' => 'CSE'],
            ['name' => 'Computer Science and Engineering', 'is_active' => true]
        );

        $dept2 = Department::firstOrCreate(
            ['code' => 'ME'],
            ['name' => 'Mechanical Engineering', 'is_active' => true]
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
            ['name' => 'Assistant Registrar (Academic)', 'password' => $password, 'role' => 'ar', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'dracademic@iiti.ac.in'],
            ['name' => 'Deputy Registrar (Academic)', 'password' => $password, 'role' => 'dr', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'academicoffice@iiti.ac.in'],
            ['name' => 'Academic Office', 'password' => $password, 'role' => 'academic_office', 'is_active' => true]
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

        $hodUserME = User::firstOrCreate(
            ['email' => 'hod_me@iiti.ac.in'],
            ['name' => 'Head of Department ME', 'password' => $password, 'role' => 'hod', 'is_active' => true]
        );
        $hodUserME->facultyProfile()->firstOrCreate(['department_id' => $dept2->id]);

        $dpgcUser = User::firstOrCreate(
            ['email' => 'dpgc@iiti.ac.in'],
            ['name' => 'DPGC Member', 'password' => $password, 'role' => 'dpgc', 'is_active' => true]
        );
        $dpgcUser->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $dpgcUserME = User::firstOrCreate(
            ['email' => 'dpgc_me@iiti.ac.in'],
            ['name' => 'DPGC Member ME', 'password' => $password, 'role' => 'dpgc', 'is_active' => true]
        );
        $dpgcUserME->facultyProfile()->firstOrCreate(['department_id' => $dept2->id]);

        // 5. Faculty Members
        $faculty1 = User::firstOrCreate(
            ['email' => 'mainsupervisor@iiti.ac.in'],
            ['name' => 'Dr. Pulkit Sharma', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty1->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty2 = User::firstOrCreate(
            ['email' => 'cosupervisor1@iiti.ac.in'],
            ['name' => 'Dr. Rajeev Sinha', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty2->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty3 = User::firstOrCreate(
            ['email' => 'cosupervisor2@iiti.ac.in'],
            ['name' => 'Dr. Sreechand', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty3->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty4 = User::firstOrCreate(
            ['email' => 'pspcmember1@iiti.ac.in'],
            ['name' => 'Dr. Rahul Gupta', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty4->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        $faculty5 = User::firstOrCreate(
            ['email' => 'pspcmember2@iiti.ac.in'],
            ['name' => 'Dr. Ankit', 'password' => $password, 'role' => 'faculty', 'is_active' => true]
        );
        $faculty5->facultyProfile()->firstOrCreate(['department_id' => $dept->id]);

        // 6. PhD Student (With existing thesis & PTS-1/PTS-2)
        $phdStudentUser = User::firstOrCreate(
            ['email' => 'phdstudent1@iiti.ac.in'],
            ['name' => 'Aarav Agarwal', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        $phdStudent = Student::updateOrCreate(
            ['roll_number' => '230001001'],
            [
                'user_id' => $phdStudentUser->id,
                'department_id' => $dept->id,
                'program_name'=>'phd',
                'admission_category' => 'TA (Teaching Assistantship)',
                'course_credits_earned' => 36.0,
                'date_joining' => '2023-08-01',
                'date_registration' => '2023-08-15',
                'date_confirmation' => '2024-08-01',
            ]
        );

        if (!$phdStudent->supervisors()->where('faculty_user_id', $faculty1->id)->exists()) {
            $phdStudent->supervisors()->attach($faculty1->id, ['supervisor_type' => 'main']);
        }

        if (!$phdStudent->supervisors()->where('faculty_user_id', $faculty2->id)->exists()) {
            $phdStudent->supervisors()->attach($faculty2->id, ['supervisor_type' => 'co']);
        }

        if (!$phdStudent->pspcMembers()->where('faculty_user_id', $faculty4->id)->exists()) {
            $phdStudent->pspcMembers()->attach($faculty4->id);
        }

        if (!$phdStudent->pspcMembers()->where('faculty_user_id', $faculty5->id)->exists()) {
            $phdStudent->pspcMembers()->attach($faculty5->id);
        }

        $phdStudentUser1 = User::firstOrCreate(
            ['email' => 'phdstudent2@iiti.ac.in'],
            ['name' => 'Aditi Sharma', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        $phdStudent1 = Student::updateOrCreate(
            ['roll_number' => '230001002'],
            [
                'user_id' => $phdStudentUser1->id,
                'department_id' => $dept2->id,
                'program_name'=>'phd',
                'admission_category' => 'FA (Fellowship Awardee)',
                'course_credits_earned' => 48.5,
                'date_joining' => '2023-08-01',
                'date_registration' => '2023-08-15',
            ]
        );


        if (!$phdStudent1->supervisors()->where('faculty_user_id', $faculty1->id)->exists()) {
            $phdStudent1->supervisors()->attach($faculty1->id, ['supervisor_type' => 'main']);
        }

        if (!$phdStudent1->supervisors()->where('faculty_user_id', $faculty2->id)->exists()) {
            $phdStudent1->supervisors()->attach($faculty2->id, ['supervisor_type' => 'co']);
        }

        if (!$phdStudent1->supervisors()->where('faculty_user_id', $faculty3->id)->exists()) {
            $phdStudent1->supervisors()->attach($faculty3->id, ['supervisor_type' => 'co']);
        }

        if (!$phdStudent1->pspcMembers()->where('faculty_user_id', $faculty4->id)->exists()) {
            $phdStudent1->pspcMembers()->attach($faculty4->id);
        }

        if (!$phdStudent1->pspcMembers()->where('faculty_user_id', $faculty5->id)->exists()) {
            $phdStudent1->pspcMembers()->attach($faculty5->id);
        }

       $msrStudentUser = User::firstOrCreate(
            ['email' => 'msrstudent1@iiti.ac.in'],
            ['name' => 'Rohan Mehta', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        $msrStudent = Student::updateOrCreate(
            ['roll_number' => '230002001'],
            [
                'user_id' => $msrStudentUser->id,
                'department_id' => $dept->id,
                'program_name'=>'msr',
                'admission_category' => 'TA (Teaching Assistantship)',
                'course_credits_earned' => 32.0,
                'date_joining' => '2023-08-01',
                'date_registration' => '2023-08-15',
            ]
        );

        if (!$msrStudent->supervisors()->where('faculty_user_id', $faculty2->id)->exists()) {
            $msrStudent->supervisors()->attach($faculty2->id, ['supervisor_type' => 'main']);
        }

        if (!$msrStudent->supervisors()->where('faculty_user_id', $faculty1->id)->exists()) {
            $msrStudent->supervisors()->attach($faculty1->id, ['supervisor_type' => 'co']);
        }

        if (!$msrStudent->pspcMembers()->where('faculty_user_id', $faculty3->id)->exists()) {
            $msrStudent->pspcMembers()->attach($faculty3->id);
        }

        if (!$msrStudent->pspcMembers()->where('faculty_user_id', $faculty4->id)->exists()) {
            $msrStudent->pspcMembers()->attach($faculty4->id);
        }

       $msrStudentUser2 = User::firstOrCreate(
            ['email' => 'msrstudent2@iiti.ac.in'],
            ['name' => 'Priya Sharma', 'password' => $password, 'role' => 'student', 'is_active' => true]
        );

        $msrStudent2 = Student::updateOrCreate(
            ['roll_number' => '230002002'],
            [
                'user_id' => $msrStudentUser2->id,
                'department_id' => $dept->id,
                'program_name'=>'msr',
                'admission_category' => 'Self-Financed',
                'course_credits_earned' => 30.5,
                'date_joining' => '2023-08-01',
                'date_registration' => '2023-08-15',
            ]
        );

        if (!$msrStudent2->supervisors()->where('faculty_user_id', $faculty2->id)->exists()) {
            $msrStudent2->supervisors()->attach($faculty2->id, ['supervisor_type' => 'main']);
        }

        if (!$msrStudent2->supervisors()->where('faculty_user_id', $faculty1->id)->exists()) {
            $msrStudent2->supervisors()->attach($faculty1->id, ['supervisor_type' => 'co']);
        }

        if (!$msrStudent2->pspcMembers()->where('faculty_user_id', $faculty4->id)->exists()) {
            $msrStudent2->pspcMembers()->attach($faculty4->id);
        }

        if (!$msrStudent2->pspcMembers()->where('faculty_user_id', $faculty5->id)->exists()) {
            $msrStudent2->pspcMembers()->attach($faculty5->id);
        }

        // 9. Seed Comment Snippets
        $this->call(CommentSnippetSeeder::class);

        $this->command->info('Successfully seeded database with PTS-1 and PTS-2 Form architectures and Fresh Student!');
    }
}