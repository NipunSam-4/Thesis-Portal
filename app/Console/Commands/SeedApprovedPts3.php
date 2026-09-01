<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts3Form;
use App\Models\Pts3Examiner;
use App\Models\Pts3OebMember;
use App\Models\User;

class SeedApprovedPts3 extends Command
{
    protected $signature = 'pts:seed-pts3 {roll=230001001}';
    protected $description = 'Directly populate an approved PTS-3 form for a student';

    public function handle(): int
    {
        $roll = $this->argument('roll');
        $student = Student::where('roll_number', $roll)->first();

        if (!$student) {
            $this->error("Student with Roll Number {$roll} not found.");
            return Command::FAILURE;
        }

        $thesis = $student->thesis;
        if (!$thesis) {
            $thesis = Thesis::create([
                'student_id' => $student->id,
                'title' => 'Machine Learning Optimization Techniques for Next-Gen Neural Networks',
                'status' => 'completed',
            ]);
        }

        $mainSup = $student->mainSupervisors->first() ?? $student->mainSupervisor;
        $coSupervisors = $student->allCoSupervisors();

        $dpgc = User::where('role', 'dpgc')->first();
        $hod = User::where('role', 'hod')->first();
        $academicOffice = User::where('role', 'academic_office')->first();
        $doaa = User::where('role', 'doaa')->first() ?? User::where('role', 'global_authority')->first();
        $senate = User::where('role', 'senate_chairperson')->first() ?? $doaa;

        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $coUser = $coSupervisors->get($i - 1);
            $coSupData["co_supervisor_{$i}_id"] = $coUser?->id;
            $coSupData["co_supervisor_{$i}_recommendation"] = $coUser ? true : null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = $coUser ? now() : null;
        }

        $now = now();

        $pts3 = Pts3Form::updateOrCreate(
            ['thesis_id' => $thesis->id],
            array_merge([
                'thesis_title' => $thesis->title,
                
                'main_supervisor_id' => $mainSup?->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => $now,
                'co_supervisors_submitted_at' => $now,

                'dpgc_user_id' => $dpgc?->id,
                'dpgc_recommendation' => true,
                'dpgc_submitted_at' => $now,

                'hod_user_id' => $hod?->id,
                'hod_recommendation' => true,
                'hod_submitted_at' => $now,

                'academic_office_user_id' => $academicOffice?->id,
                'academic_office_is_verified' => true,
                'academic_office_submitted_at' => $now,

                'doaa_user_id' => $doaa?->id,
                'doaa_is_verified' => true,
                'doaa_submitted_at' => $now,

                'senate_chairperson_user_id' => $senate?->id,
                'senate_chairperson_approval' => true,
                'senate_chairperson_approval_remark' => 'Approved as recommended.',
                'senate_chairperson_submitted_at' => $now,

                'status' => 'approved',
                'current_stage' => 'completed',
                'approved_by_authority' => 'Senate Chairperson',
            ], $coSupData)
        );

        // Delete existing examiners and OEB members to re-seed cleanly
        Pts3Examiner::where('pts3_form_id', $pts3->id)->delete();
        Pts3OebMember::where('pts3_form_id', $pts3->id)->delete();

        // 2 Indian Examiners
        Pts3Examiner::create([
            'pts3_form_id' => $pts3->id,
            'type' => 'indian',
            'name' => 'Prof. Rajesh K. Sharma',
            'designation' => 'Professor',
            'organization' => 'IIT Delhi',
            'postal_address' => 'Department of CSE, IIT Delhi, Hauz Khas, New Delhi 110016',
            'email' => 'rksharma@cse.iitd.ac.in',
            'phone_number' => '9811223344',
            'phone_country_code' => '+91',
            'phone_iso2' => 'in',
            'website' => 'https://www.iitd.ac.in/~rksharma',
            'research_area' => 'Neural Network Optimization, Deep Learning Systems',
            'has_consent' => true,
            'doaa_priority' => 1,
            'senate_chairperson_priority' => 1,
        ]);

        Pts3Examiner::create([
            'pts3_form_id' => $pts3->id,
            'type' => 'indian',
            'name' => 'Prof. Amitabha Mukerjee',
            'designation' => 'Senior Professor',
            'organization' => 'IIT Kanpur',
            'postal_address' => 'Department of CSE, IIT Kanpur, Uttar Pradesh 208016',
            'email' => 'mukerjee@cse.iitk.ac.in',
            'phone_number' => '9450123456',
            'phone_country_code' => '+91',
            'phone_iso2' => 'in',
            'website' => 'https://www.iitk.ac.in/~mukerjee',
            'research_area' => 'Artificial Intelligence and Cognitive Robotics',
            'has_consent' => true,
            'doaa_priority' => 2,
            'senate_chairperson_priority' => 2,
        ]);

        // 2 International Examiners
        Pts3Examiner::create([
            'pts3_form_id' => $pts3->id,
            'type' => 'international',
            'name' => 'Prof. David A. Miller',
            'designation' => 'Chair Professor',
            'organization' => 'Stanford University',
            'postal_address' => 'Gates Computer Science Building, Stanford University, CA 94305, USA',
            'email' => 'damiller@cs.stanford.edu',
            'phone_number' => '6507232300',
            'phone_country_code' => '+1',
            'phone_iso2' => 'us',
            'website' => 'https://cs.stanford.edu/~damiller',
            'research_area' => 'High-Performance Machine Learning Accelerators',
            'has_consent' => true,
            'doaa_priority' => 1,
            'senate_chairperson_priority' => 1,
        ]);

        Pts3Examiner::create([
            'pts3_form_id' => $pts3->id,
            'type' => 'international',
            'name' => 'Prof. Elena Rostova',
            'designation' => 'Professor of Computer Science',
            'organization' => 'ETH Zurich',
            'postal_address' => 'Department of Computer Science, ETH Zurich, Switzerland',
            'email' => 'erostova@inf.ethz.ch',
            'phone_number' => '446321111',
            'phone_country_code' => '+41',
            'phone_iso2' => 'ch',
            'website' => 'https://inf.ethz.ch/~erostova',
            'research_area' => 'Parallel Algorithms & AI Systems',
            'has_consent' => true,
            'doaa_priority' => 2,
            'senate_chairperson_priority' => 2,
        ]);

        // 4 OEB Members
        $oebList = [
            ['name' => 'Dr. Subhashish Banerjee', 'designation' => 'Associate Professor', 'department' => 'CSE', 'email' => 'subhashish@iitg.ac.in'],
            ['name' => 'Dr. Meenakshi D\'Souza', 'designation' => 'Associate Professor', 'department' => 'EEE', 'email' => 'meenakshi@iitg.ac.in'],
            ['name' => 'Dr. Hemant B. Kaushik', 'designation' => 'Professor', 'department' => 'Civil', 'email' => 'hemant@iitg.ac.in'],
            ['name' => 'Dr. Anamika Barua', 'designation' => 'Professor', 'department' => 'HSS', 'email' => 'anamika@iitg.ac.in'],
        ];

        foreach ($oebList as $idx => $oeb) {
            Pts3OebMember::create([
                'pts3_form_id' => $pts3->id,
                'name' => $oeb['name'],
                'designation' => $oeb['designation'],
                'department' => $oeb['department'],
                'email' => $oeb['email'],
                'phone_number' => '987650000' . ($idx + 1),
                'phone_country_code' => '+91',
                'phone_iso2' => 'in',
                'doaa_priority' => $idx + 1,
                'senate_chairperson_priority' => $idx + 1,
            ]);
        }

        $this->info("✓ Approved PTS-3 Form created for student {$roll} (Form ID: {$pts3->id}).");
        return Command::SUCCESS;
    }
}
