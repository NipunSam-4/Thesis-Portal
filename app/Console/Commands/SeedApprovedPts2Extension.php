<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts2Extension;
use App\Models\User;

class SeedApprovedPts2Extension extends Command
{
    protected $signature = 'pts:seed-pts2-extension {roll=230001001}';
    protected $description = 'Directly populate an approved PTS-2 Extension for a student';

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

        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $coUser = $coSupervisors->get($i - 1);
            $coSupData["co_supervisor_{$i}_id"] = $coUser?->id;
            $coSupData["co_supervisor_{$i}_recommendation"] = $coUser ? true : null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = $coUser ? now() : null;
        }

        $now = now();
        $extendedDate = now()->addMonths(6)->toDateString();

        $ext = Pts2Extension::updateOrCreate(
            ['thesis_id' => $thesis->id],
            array_merge([
                'reason_for_extension' => 'Additional experimental validation required for deep learning model training.',
                'extended_until_date' => $extendedDate,
                'approved_extended_until_date' => $extendedDate,
                
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
                'academic_office_recommendation' => true,
                'academic_office_submitted_at' => $now,

                'doaa_user_id' => $doaa?->id,
                'doaa_recommendation' => true,
                'doaa_submitted_at' => $now,

                'status' => 'approved',
                'current_stage' => 'completed',
                'approved_by_id' => $doaa?->id,
            ], $coSupData)
        );

        $this->info("✓ Approved PTS-2 Extension created for student {$roll} (Extension ID: {$ext->id}).");
        return Command::SUCCESS;
    }
}
