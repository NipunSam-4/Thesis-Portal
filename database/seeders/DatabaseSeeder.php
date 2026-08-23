<?php

namespace Database\Seeders;

use App\Models\ActingDoaa;
use App\Models\Admin;
use App\Models\Department;
use App\Models\DeptAuthorityProfile;
use App\Models\FacultyProfile;
use App\Models\Pts1Form;
use App\Models\Pts2Extension;
use App\Models\Pts2Form;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\User;
use App\Models\VestedDoaa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Seed the application's database.
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1. Departments Configuration
        $departmentsData = [
            'CSE' => [
                'name' => 'Computer Science and Engineering',
                'hod' => ['email' => 'hodcse@iiti.ac.in', 'name' => 'Dr. Aruna Tiwari'],
                'dpgc' => ['email' => 'dpgccse@iiti.ac.in', 'name' => 'Dr. Bodhisatwa Mazumdar'],
                'faculty' => [
                    ['email' => 'mainsupervisor@iiti.ac.in', 'name' => 'Dr. Pulkit Sharma'],
                    ['email' => 'cosupervisor1@iiti.ac.in', 'name' => 'Dr. Rajeev Sinha'],
                    ['email' => 'cosupervisor2@iiti.ac.in', 'name' => 'Dr. Sreechand'],
                    ['email' => 'pspcmember1@iiti.ac.in', 'name' => 'Dr. Rahul Gupta'],
                    ['email' => 'pspcmember2@iiti.ac.in', 'name' => 'Dr. Ankit'],
                ],
                'students' => [
                    ['email' => 'csestudent1@iiti.ac.in', 'name' => 'Aarav Agarwal', 'roll' => '230001001', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' =>null ],
                    ['email' => 'csestudent2@iiti.ac.in', 'name' => 'Vikram Seth', 'roll' => '230001002', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => null],
                    ['email' => 'csestudent3@iiti.ac.in', 'name' => 'Rohan Mehta', 'roll' => '230002001', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 32.0],
                    ['email' => 'csestudent4@iiti.ac.in', 'name' => 'Priya Sharma', 'roll' => '230002002', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 30.5],
                ],
            ],
            'ME' => [
                'name' => 'Mechanical Engineering',
                'hod' => ['email' => 'hodme@iiti.ac.in', 'name' => 'Dr. Santosh K. Sahu'],
                'dpgc' => ['email' => 'dpgcme@iiti.ac.in', 'name' => 'Dr. I. A. Palani'],
                'faculty' => [
                    ['email' => 'faculty_me1@iiti.ac.in', 'name' => 'Dr. Shanmugam Neethi'],
                    ['email' => 'faculty_me2@iiti.ac.in', 'name' => 'Dr. Devendra Deshmukh'],
                    ['email' => 'faculty_me3@iiti.ac.in', 'name' => 'Dr. Ritunesh Kumar'],
                ],
                'students' => [
                    ['email' => 'mestudent1@iiti.ac.in', 'name' => 'Aditi Sharma', 'roll' => '230001011', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 48.5],
                    ['email' => 'mestudent2@iiti.ac.in', 'name' => 'Karan Verma', 'roll' => '230001012', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 40.0],
                    ['email' => 'mestudent3@iiti.ac.in', 'name' => 'Siddharth Nair', 'roll' => '230002011', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 34.0],
                    ['email' => 'mestudent4@iiti.ac.in', 'name' => 'Ananya Desai', 'roll' => '230002012', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 31.0],
                ],
            ],
            'EE' => [
                'name' => 'Electrical Engineering',
                'hod' => ['email' => 'hodee@iiti.ac.in', 'name' => 'Dr. Ram Bilas Pachori'],
                'dpgc' => ['email' => 'dpgcee@iiti.ac.in', 'name' => 'Dr. Vimal Bhatia'],
                'faculty' => [
                    ['email' => 'faculty_ee1@iiti.ac.in', 'name' => 'Dr. Abhinoy Kumar Singh'],
                    ['email' => 'faculty_ee2@iiti.ac.in', 'name' => 'Dr. Trapti Jain'],
                    ['email' => 'faculty_ee3@iiti.ac.in', 'name' => 'Dr. Mukesh Kumar'],
                ],
                'students' => [
                    ['email' => 'eestudent1@iiti.ac.in', 'name' => 'Rahul Nambiar', 'roll' => '230001021', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 38.0],
                    ['email' => 'eestudent2@iiti.ac.in', 'name' => 'Sneha Iyer', 'roll' => '230001022', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 44.0],
                    ['email' => 'eestudent3@iiti.ac.in', 'name' => 'Arjun Kapoor', 'roll' => '230002021', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 33.0],
                    ['email' => 'eestudent4@iiti.ac.in', 'name' => 'Neha Kulkarni', 'roll' => '230002022', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 31.5],
                ],
            ],
            'MEMS' => [
                'name' => 'Metallurgy Engineering and Materials Science',
                'hod' => ['email' => 'hodmems@iiti.ac.in', 'name' => 'Dr. Parasharam M. Shirage'],
                'dpgc' => ['email' => 'dpgcmems@iiti.ac.in', 'name' => 'Dr. Ajay Kumar Kushwaha'],
                'faculty' => [
                    ['email' => 'faculty_mems1@iiti.ac.in', 'name' => 'Dr. Sunil Kumar'],
                    ['email' => 'faculty_mems2@iiti.ac.in', 'name' => 'Dr. Mrigendra Dubey'],
                    ['email' => 'faculty_mems3@iiti.ac.in', 'name' => 'Dr. Vinod Kumar'],
                ],
                'students' => [
                    ['email' => 'memsstudent1@iiti.ac.in', 'name' => 'Gaurav Bhatt', 'roll' => '230001031', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 36.5],
                    ['email' => 'memsstudent2@iiti.ac.in', 'name' => 'Pooja Hegde', 'roll' => '230001032', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 45.0],
                    ['email' => 'memsstudent3@iiti.ac.in', 'name' => 'Devendra Joshi', 'roll' => '230002031', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 32.0],
                    ['email' => 'memsstudent4@iiti.ac.in', 'name' => 'Meera Singhania', 'roll' => '230002032', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 30.0],
                ],
            ],
            'CE' => [
                'name' => 'Civil Engineering',
                'hod' => ['email' => 'hodce@iiti.ac.in', 'name' => 'Dr. Sandeep Chaudhary'],
                'dpgc' => ['email' => 'dpgcce@iiti.ac.in', 'name' => 'Dr. Neelima Satyam'],
                'faculty' => [
                    ['email' => 'faculty_ce1@iiti.ac.in', 'name' => 'Dr. Munir Nayak'],
                    ['email' => 'faculty_ce2@iiti.ac.in', 'name' => 'Dr. Lalit Borana'],
                    ['email' => 'faculty_ce3@iiti.ac.in', 'name' => 'Dr. Abhishek Rajput'],
                ],
                'students' => [
                    ['email' => 'cestudent1@iiti.ac.in', 'name' => 'Manish Tiwari', 'roll' => '230001041', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 37.0],
                    ['email' => 'cestudent2@iiti.ac.in', 'name' => 'Divya Patel', 'roll' => '230001042', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 43.0],
                    ['email' => 'cestudent3@iiti.ac.in', 'name' => 'Ravi Shastri', 'roll' => '230002041', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 33.5],
                    ['email' => 'cestudent4@iiti.ac.in', 'name' => 'Swati Mishra', 'roll' => '230002042', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 31.0],
                ],
            ],
            'CHE' => [
                'name' => 'Chemical Engineering',
                'hod' => ['email' => 'hodche@iiti.ac.in', 'name' => 'Dr. Sanjay Kumar Kar'],
                'dpgc' => ['email' => 'dpgcche@iiti.ac.in', 'name' => 'Dr. Hemant Kumar'],
                'faculty' => [
                    ['email' => 'faculty_che1@iiti.ac.in', 'name' => 'Dr. Avinash Sonwani'],
                    ['email' => 'faculty_che2@iiti.ac.in', 'name' => 'Dr. Jayanta Debnath'],
                    ['email' => 'faculty_che3@iiti.ac.in', 'name' => 'Dr. Tushar Sharma'],
                ],
                'students' => [
                    ['email' => 'chestudent1@iiti.ac.in', 'name' => 'Nikhil Bansal', 'roll' => '230001051', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 39.0],
                    ['email' => 'chestudent2@iiti.ac.in', 'name' => 'Tanvi Rao', 'roll' => '230001052', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 46.0],
                    ['email' => 'chestudent3@iiti.ac.in', 'name' => 'Akash Saxena', 'roll' => '230002051', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 34.0],
                    ['email' => 'chestudent4@iiti.ac.in', 'name' => 'Ritu Sen', 'roll' => '230002052', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 30.5],
                ],
            ],
            'MATH' => [
                'name' => 'Mathematics',
                'hod' => ['email' => 'hodmath@iiti.ac.in', 'name' => 'Dr. Swadesh Kumar Sahoo'],
                'dpgc' => ['email' => 'dpgcmath@iiti.ac.in', 'name' => 'Dr. Niraj Kumar Shukla'],
                'faculty' => [
                    ['email' => 'faculty_math1@iiti.ac.in', 'name' => 'Dr. Anand Parkash'],
                    ['email' => 'faculty_math2@iiti.ac.in', 'name' => 'Dr. Ashisha Kumar'],
                    ['email' => 'faculty_math3@iiti.ac.in', 'name' => 'Dr. M. Tanveer'],
                ],
                'students' => [
                    ['email' => 'mathstudent1@iiti.ac.in', 'name' => 'Kavita Raman', 'roll' => '230001061', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 40.0],
                    ['email' => 'mathstudent2@iiti.ac.in', 'name' => 'Deepak Choudhary', 'roll' => '230001062', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 44.5],
                    ['email' => 'mathstudent3@iiti.ac.in', 'name' => 'Shreya Mukherjee', 'roll' => '230002061', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 32.0],
                    ['email' => 'mathstudent4@iiti.ac.in', 'name' => 'Prateek Jain', 'roll' => '230002062', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 30.0],
                ],
            ],
            'PHY' => [
                'name' => 'Physics',
                'hod' => ['email' => 'hodphy@iiti.ac.in', 'name' => 'Dr. Preeti Anand Bhobe'],
                'dpgc' => ['email' => 'dpgcphy@iiti.ac.in', 'name' => 'Dr. Raghunath Sahoo'],
                'faculty' => [
                    ['email' => 'faculty_phy1@iiti.ac.in', 'name' => 'Dr. Sarika Yadav'],
                    ['email' => 'faculty_phy2@iiti.ac.in', 'name' => 'Dr. Rajesh Kumar'],
                    ['email' => 'faculty_phy3@iiti.ac.in', 'name' => 'Dr. Aloke Kanjilal'],
                ],
                'students' => [
                    ['email' => 'phystudent1@iiti.ac.in', 'name' => 'Sanjay Menon', 'roll' => '230001071', 'prog' => 'phd', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 38.5],
                    ['email' => 'phystudent2@iiti.ac.in', 'name' => 'Isha Ghosh', 'roll' => '230001072', 'prog' => 'phd', 'cat' => 'FA (Fellowship Awardee)', 'credits' => 43.0],
                    ['email' => 'phystudent3@iiti.ac.in', 'name' => 'Varun Kashyap', 'roll' => '230002071', 'prog' => 'msr', 'cat' => 'TA (Teaching Assistantship)', 'credits' => 33.0],
                    ['email' => 'phystudent4@iiti.ac.in', 'name' => 'Ruchika Roy', 'roll' => '230002072', 'prog' => 'msr', 'cat' => 'Self-Financed', 'credits' => 31.0],
                ],
            ],
        ];

        // 2. Base Admin Model Accounts (System Admin & Department Super Admins)
        Admin::firstOrCreate(
            ['email' => 'systemadmin@iiti.ac.in'],
            ['name' => 'System Admin', 'password' => $password, 'admin_type' => 'system_admin', 'is_active' => true]
        );

        // 3. Global Authorities (Portal Users)
        $globalAuthorities = [
            ['email' => 'doaa@iiti.ac.in', 'name' => 'Dean of Academic Affairs', 'role' => 'doaa'],
            ['email' => 'actingdoaa@iiti.ac.in', 'name' => 'Acting DOAA', 'role' => 'acting_approval_authority'],
            ['email' => 'vesteddoaa@iiti.ac.in', 'name' => 'Vested DOAA', 'role' => 'acting_approval_authority'],
            ['email' => 'actingvesteddoaa@iiti.ac.in', 'name' => 'Acting Vested DOAA', 'role' => 'acting_approval_authority'],
            ['email' => 'adoaa@iiti.ac.in', 'name' => 'Associate Dean of Academic Affairs', 'role' => 'adoaa'],
            ['email' => 'senatechairperson@iiti.ac.in', 'name' => 'Senate Chairperson', 'role' => 'senate_chairperson'],
            ['email' => 'aracademic@iiti.ac.in', 'name' => 'Assistant Registrar (Academic)', 'role' => 'ar'],
            ['email' => 'dracademic@iiti.ac.in', 'name' => 'Deputy Registrar (Academic)', 'role' => 'dr'],
            ['email' => 'academicoffice@iiti.ac.in', 'name' => 'Academic Office', 'role' => 'academic_office'],
            ['email' => 'sectionofficer@iiti.ac.in', 'name' => 'Section Officer', 'role' => 'section_officer'],
        ];

        foreach ($globalAuthorities as $ga) {
            $u = User::firstOrCreate(
                ['email' => $ga['email']],
                ['name' => $ga['name'], 'password' => $password, 'role' => $ga['role'], 'is_active' => true]
            );

            if ($ga['email'] === 'actingdoaa@iiti.ac.in') {
                ActingDoaa::firstOrCreate(['user_id' => $u->id], ['is_active' => true]);
            } elseif ($ga['email'] === 'vesteddoaa@iiti.ac.in') {
                VestedDoaa::firstOrCreate(['user_id' => $u->id], ['is_active' => true]);
            } elseif ($ga['email'] === 'actingvesteddoaa@iiti.ac.in') {
                ActingDoaa::firstOrCreate(['user_id' => $u->id], ['is_active' => true]);
                VestedDoaa::firstOrCreate(['user_id' => $u->id], ['is_active' => true]);
            }
        }

        // 4. Seed Departments, Authorities, Faculty, and Students
        foreach ($departmentsData as $code => $data) {
            $department = Department::firstOrCreate(
                ['code' => $code],
                ['name' => $data['name'], 'is_active' => true]
            );

            // Department Super Admin
            Admin::firstOrCreate(
                ['email' => 'superadmin_' . strtolower($code) . '@iiti.ac.in'],
                ['name' => $data['name'] . ' Super Admin', 'password' => $password, 'admin_type' => 'super_admin', 'department_id' => $department->id, 'is_active' => true]
            );

            // HOD
            $hod = User::firstOrCreate(
                ['email' => $data['hod']['email']],
                ['name' => $data['hod']['name'], 'password' => $password, 'role' => 'hod', 'is_active' => true]
            );
            $hod->deptAuthorityProfile()->firstOrCreate(['department_id' => $department->id]);

            // DPGC
            $dpgc = User::firstOrCreate(
                ['email' => $data['dpgc']['email']],
                ['name' => $data['dpgc']['name'], 'password' => $password, 'role' => 'dpgc', 'is_active' => true]
            );
            $dpgc->deptAuthorityProfile()->firstOrCreate(['department_id' => $department->id]);

            // Faculty Members
            $facultyUsers = [];
            foreach ($data['faculty'] as $fac) {
                $faculty = User::firstOrCreate(
                    ['email' => $fac['email']],
                    ['name' => $fac['name'], 'password' => $password, 'role' => 'faculty', 'is_active' => true]
                );
                $faculty->facultyProfile()->firstOrCreate(['department_id' => $department->id]);
                $facultyUsers[] = $faculty;
            }

            // Students and Committee Assignments
            // Committee configuration per student index:
            // Student 0: Main = Fac 0, Co = Fac 1, PSPC = [Fac 2, Fac 0/HOD]
            // Student 1: Main = Fac 0, Co = None,  PSPC = [Fac 1, Fac 2]
            // Student 2: Main = Fac 1, Co = Fac 0, PSPC = [Fac 2, Fac 0]
            // Student 3: Main = Fac 2, Co = Fac 1, PSPC = [Fac 0, Fac 1]
            $facCount = count($facultyUsers);

            foreach ($data['students'] as $idx => $stData) {
                $studentUser = User::firstOrCreate(
                    ['email' => $stData['email']],
                    ['name' => $stData['name'], 'password' => $password, 'role' => 'student', 'is_active' => true]
                );

                $student = Student::updateOrCreate(
                    ['roll_number' => $stData['roll']],
                    [
                        'user_id' => $studentUser->id,
                        'department_id' => $department->id,
                        'program_name' => $stData['prog'],
                        'admission_category' => $stData['cat'],
                        'course_credits_earned' => $stData['credits'],
                        'course_credits_required' => 36.0,
                        'date_joining' => '2023-08-01',
                        'date_registration' => '2023-08-15',
                    ]
                );

                // Determine Main Supervisor, Co-Supervisor, and PSPC members for this student
                $mainSup = $facultyUsers[$idx % $facCount] ?? $facultyUsers[0];
                
                // Co-supervisor (assigned for students 0, 2, 3; omitted for student 1)
                $coSup = ($idx !== 1) ? ($facultyUsers[($idx + 1) % $facCount] ?? null) : null;

                // PSPC members (2 distinct faculty members different from main supervisor)
                $pspc1 = $facultyUsers[($idx + 1) % $facCount] ?? $facultyUsers[0];
                $pspc2 = $facultyUsers[($idx + 2) % $facCount] ?? $facultyUsers[0];

                // Attach Main Supervisor
                if (!$student->supervisors()->where('faculty_user_id', $mainSup->id)->exists()) {
                    $student->supervisors()->attach($mainSup->id, ['supervisor_type' => 'main']);
                }

                // Attach Co-Supervisor (if assigned)
                if ($coSup && !$student->supervisors()->where('faculty_user_id', $coSup->id)->exists()) {
                    $student->supervisors()->attach($coSup->id, ['supervisor_type' => 'co']);
                }

                // Attach PSPC Members
                if (!$student->pspcMembers()->where('faculty_user_id', $pspc1->id)->exists()) {
                    $student->pspcMembers()->attach($pspc1->id);
                }

                if ($pspc2->id !== $pspc1->id && !$student->pspcMembers()->where('faculty_user_id', $pspc2->id)->exists()) {
                    $student->pspcMembers()->attach($pspc2->id);
                }
            }
        }

        // 5. Seed Comment Snippets
        $this->call(CommentSnippetSeeder::class);

        $this->command->info('Successfully seeded all 8 departments, HODs, DPGCs, Faculty, and Students (PhD & MS(R))!');
    }
}