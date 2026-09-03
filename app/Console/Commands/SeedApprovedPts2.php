<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts2Form;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SeedApprovedPts2 extends Command
{
    protected $signature = 'pts:seed-pts2 {roll=230001001}';
    protected $description = 'Directly populate an approved PTS-2 form for a student';

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

        // Sample document path
        $dir = "students/{$roll}/thesis_{$thesis->id}/pts2/approved";
        $synopsisReportPath = "{$dir}/{$roll}_PTS2_Synopsis_Report_Student.pdf";

        Storage::disk('local')->makeDirectory($dir);
        Storage::disk('local')->put($synopsisReportPath, '%PDF-1.4 Mock Approved Synopsis Report');

        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $coUser = $coSupervisors->get($i - 1);
            $coSupData["co_supervisor_{$i}_id"] = $coUser?->id;
            $coSupData["co_supervisor_{$i}_recommendation"] = $coUser ? true : null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = $coUser ? now() : null;
        }

        $now = now();

        $pts2 = Pts2Form::updateOrCreate(
            ['thesis_id' => $thesis->id],
            array_merge([
                'thesis_title' => $thesis->title,
                'synopsis_report_doc_path' => $synopsisReportPath,
                'course_credits_student' => $student->course_credits_earned ?? 42.0,
                'date_of_submission' => now()->subDays(5)->toDateString(),
                'current_address' => 'PhD Hostel 3, Room 204, IIT Guwahati, Assam 781039',
                'alternate_email' => 'csestudent1.alt@gmail.com',
                'recent_phone_number' => '9876543210',
                'recent_phone_country_code' => '+91',
                'recent_phone_iso2' => 'in',
                'cert_prima_facie_case' => true,
                'cert_no_prior_degree_submission' => true,
                'collaborative_work_status' => false,

                'main_supervisor_thesis_title' => $thesis->title,
                'main_supervisor_synopsis_report_doc_path' => $synopsisReportPath,
                'main_supervisor_cert_prima_facie_case' => true,
                'main_supervisor_cert_no_prior_degree_submission' => true,
                'main_supervisor_collaborative_work_status' => false,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => $now,
                'co_supervisors_submitted_at' => $now,

                'academic_office_user_id' => $academicOffice?->id,
                'academic_office_is_verified' => true,
                'academic_office_submitted_at' => $now,

                'doaa_user_id' => $doaa?->id,
                'doaa_approval' => true,
                'doaa_submitted_at' => $now,

                'status' => 'approved',
                'current_stage' => 'completed',
                'approved_by_id' => $doaa?->id,
            ], $coSupData)
        );

        $this->info("✓ Approved PTS-2 Form created for student {$roll} (Form ID: {$pts2->id}).");
        return Command::SUCCESS;
    }
}
