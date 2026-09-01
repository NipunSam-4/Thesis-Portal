<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Thesis;
use App\Models\Pts1Form;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SeedApprovedPts1 extends Command
{
    protected $signature = 'pts:seed-pts1 {roll=230001001}';
    protected $description = 'Directly populate an approved PTS-1 form for a student';

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
        } else {
            $thesis->update([
                'title' => 'Machine Learning Optimization Techniques for Next-Gen Neural Networks',
                'status' => 'completed',
            ]);
        }

        $mainSup = $student->mainSupervisors->first() ?? $student->mainSupervisor;
        $coSupervisors = $student->allCoSupervisors();
        $pspcMembers = $student->pspcMembers;

        $dpgc = User::where('role', 'dpgc')->first();
        $hod = User::where('role', 'hod')->first();
        $academicOffice = User::where('role', 'academic_office')->first();
        $doaa = User::where('role', 'doaa')->first() ?? User::where('role', 'global_authority')->first();

        // Sample document paths
        $dir = "students/{$roll}/thesis_{$thesis->id}/pts1/approved";
        $synopsisPath = "{$dir}/{$roll}_PTS1_Draft_Synopsis_Student.pdf";
        $pubListPath = "{$dir}/{$roll}_PTS1_Publication_List_Student.xlsx";

        Storage::disk('local')->makeDirectory($dir);
        Storage::disk('local')->put($synopsisPath, '%PDF-1.4 Mock Approved Draft Synopsis');
        Storage::disk('local')->put($pubListPath, 'Mock Approved Publication List');

        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $coUser = $coSupervisors->get($i - 1);
            $coSupData["co_supervisor_{$i}_id"] = $coUser?->id;
            $coSupData["co_supervisor_{$i}_recommendation"] = $coUser ? true : null;
            $coSupData["co_supervisor_{$i}_submitted_at"] = $coUser ? now() : null;
        }

        $pspcData = [];
        for ($i = 1; $i <= 4; $i++) {
            $pspcUser = $pspcMembers->get($i - 1);
            $pspcData["pspc_member_{$i}_id"] = $pspcUser?->id;
            $pspcData["pspc_member_{$i}_recommendation"] = $pspcUser ? true : null;
            $pspcData["pspc_member_{$i}_submitted_at"] = $pspcUser ? now() : null;
        }

        $now = now();

        $pts1 = Pts1Form::updateOrCreate(
            ['thesis_id' => $thesis->id],
            array_merge([
                'thesis_title' => $thesis->title,
                'seminar_date' => now()->subDays(10)->toDateString(),
                'seminar_time' => '10:00 AM',
                'seminar_venue' => 'Seminar Hall 1, CSE Dept',
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'draft_synopsis_report_doc_path' => $synopsisPath,
                'publication_list_doc_path' => $pubListPath,
                'publication_norm_fulfillment' => true,
                'min_time_req_fulfilled' => true,
                
                'main_supervisor_id' => $mainSup?->id,
                'main_supervisor_recommendation' => true,
                'main_supervisor_submitted_at' => $now,
                'co_supervisors_submitted_at' => $now,
                'pspc_members_submitted_at' => $now,

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
                'doaa_approval' => true,
                'doaa_submitted_at' => $now,

                'status' => 'approved',
                'current_stage' => 'completed',
                'approved_by_authority' => 'DOAA',
            ], $coSupData, $pspcData)
        );

        $this->info("✓ Approved PTS-1 Form created for student {$roll} (Form ID: {$pts1->id}).");
        return Command::SUCCESS;
    }
}
