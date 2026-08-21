<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pts1Form;
use App\Models\Pts2Extension;

class AutoRecommendWorkflow extends Command
{
    // The name and signature of the console command.
    // @var string
    protected $signature = 'workflow:auto-recommend {--minutes= : Override inactivity threshold in minutes for quick testing} {--hours= : Override inactivity threshold in hours}';

    // The console command description.
    // @var string
    protected $description = 'Auto-recommend pending PTS-1 and PTS-2 Extension stages after inactivity from previous authority submission.';

    // Execute the console command.
    public function handle()
    {
        $minutesOpt = $this->option('minutes');
        $hoursOpt = $this->option('hours');

        if ($minutesOpt !== null) {
            $pts1Minutes = (int) $minutesOpt;
            $pts2Minutes = (int) $minutesOpt;
        } elseif ($hoursOpt !== null) {
            $pts1Minutes = (int) $hoursOpt * 60;
            $pts2Minutes = (int) $hoursOpt * 60;
        } else {
            $pts1Minutes = (int) config('workflow.pts1_minutes');
            $pts2Minutes = (int) config('workflow.pts2_extension_minutes');
        }

        $this->info("Starting Auto-Recommendation workflow check (PTS-1: {$pts1Minutes} min, PTS-2 Extension: {$pts2Minutes} min)...");

        $this->processPts1Forms($pts1Minutes);
        $this->processPts2Extensions($pts2Minutes);

        $this->info('Auto-Recommendation workflow check completed successfully.');
        return Command::SUCCESS;
    }

    // Safely set a model attribute only if the physical database column exists.
    protected function setIfColumnExists($model, string $column, $value): void
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), $column)) {
            $model->{$column} = $value;
        }
    }

    // Process PTS-1 forms.
    protected function processPts1Forms(int $minutes)
    {
        $forms = Pts1Form::where('status', 'in_progress')->get();

        if ($forms->isEmpty()) {
            $this->line(" - No PTS-1 forms currently 'in_progress'.");
            return;
        }

        foreach ($forms as $pts1) {
            $updated = false;

            // 0. Main Supervisor Stage (Manual Evaluation Required - No Auto-Recommendation)
            if ($pts1->current_stage === 'main_supervisor') {
                $this->line(" - PTS-1 Form #{$pts1->id} (main_supervisor stage): Requires manual evaluation by Main Supervisor.");
            }
            // 1. Co-Supervisors Stage (Parallel Auto-Recommendation)
            elseif ($pts1->current_stage === 'co_supervisors') {
                $mainSubAt = $pts1->main_supervisor_submitted_at ?? $pts1->pts1_submitted_at ?? $pts1->updated_at ?? $pts1->created_at;
                $elapsed = $mainSubAt ? (int) abs(now()->diffInMinutes($mainSubAt)) : $minutes + 1;
                if ($elapsed >= $minutes) {
                    for ($i = 1; $i <= 10; $i++) {
                        $coSupId = $pts1->{"co_supervisor_{$i}_id"};
                        if ($coSupId && is_null($pts1->{"co_supervisor_{$i}_recommendation"})) {
                            $pts1->{"co_supervisor_{$i}_recommendation"} = true;
                            $pts1->{"co_supervisor_{$i}_confidential_remark"} = 'Auto-recommended';
                            $updated = true;
                        }
                    }
                    $this->setIfColumnExists($pts1, 'co_supervisors_submitted_at', now());

                    // Check if PSPC members exist
                    $hasPspc = false;
                    for ($i = 1; $i <= 10; $i++) {
                        if ($pts1->{"pspc_member_{$i}_id"}) { $hasPspc = true; break; }
                    }

                    $pts1->current_stage = $hasPspc ? 'pspc_members' : 'dpgc';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: All Co-Supervisors auto-recommended in parallel after {$elapsed}m (threshold {$minutes}m). Stage advanced to {$pts1->current_stage}.");
                } else {
                    $this->line(" - PTS-1 Form #{$pts1->id} (co_supervisors stage): Elapsed {$elapsed}m / Required {$minutes}m.");
                }
            }
            // 2. PSPC Committee Members Stage (Parallel Auto-Recommendation)
            elseif ($pts1->current_stage === 'pspc_members') {
                $prevTimestamp = $pts1->co_supervisors_submitted_at ?? $pts1->main_supervisor_submitted_at ?? $pts1->created_at;
                $elapsed = $prevTimestamp ? (int) abs(now()->diffInMinutes($prevTimestamp)) : $minutes + 1;
                if ($elapsed >= $minutes) {
                    for ($i = 1; $i <= 10; $i++) {
                        $pspcId = $pts1->{"pspc_member_{$i}_id"};
                        if ($pspcId && is_null($pts1->{"pspc_member_{$i}_recommendation"})) {
                            $pts1->{"pspc_member_{$i}_recommendation"} = true;
                            $pts1->{"pspc_member_{$i}_confidential_remark"} = 'Auto-recommended';
                            $updated = true;
                        }
                    }
                    $this->setIfColumnExists($pts1, 'pspc_members_submitted_at', now());
                    $pts1->current_stage = 'dpgc';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: All PSPC Members auto-recommended in parallel after {$elapsed}m (threshold {$minutes}m). Stage advanced to dpgc.");
                } else {
                    $this->line(" - PTS-1 Form #{$pts1->id} (pspc_members stage): Elapsed {$elapsed}m / Required {$minutes}m.");
                }
            }
            // 3. DPGC Convenor Stage
            elseif ($pts1->current_stage === 'dpgc' && is_null($pts1->dpgc_submitted_at ?? null)) {
                $prevTimestamp = $pts1->pspc_members_submitted_at ?? $pts1->co_supervisors_submitted_at ?? $pts1->main_supervisor_submitted_at ?? $pts1->created_at;
                $elapsed = $prevTimestamp ? (int) abs(now()->diffInMinutes($prevTimestamp)) : $minutes + 1;
                if ($elapsed >= $minutes) {
                    $pts1->dpgc_recommendation = true;
                    $pts1->dpgc_confidential_remark = 'Auto-recommended';
                    $this->setIfColumnExists($pts1, 'dpgc_submitted_at', now());
                    $pts1->current_stage = 'hod';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: DPGC auto-recommended after {$elapsed}m (threshold {$minutes}m). Stage advanced to hod.");
                } else {
                    $this->line(" - PTS-1 Form #{$pts1->id} (dpgc stage): Elapsed {$elapsed}m / Required {$minutes}m.");
                }
            }

            if ($updated) {
                $pts1->save();
            }
        }
    }

    // Process PTS-2 Extensions.
    protected function processPts2Extensions(int $minutes)
    {
        $extensions = Pts2Extension::where('status', 'in_progress')->get();

        if ($extensions->isEmpty()) {
            $this->line(" - No PTS-2 Extensions currently 'in_progress'.");
            return;
        }

        foreach ($extensions as $extension) {
            $updated = false;

            // 1. Main Supervisor Stage for PTS-2 Extension (Manual Evaluation Required - No Auto-Recommendation)
            if ($extension->current_stage === 'main_supervisor') {
                $this->line(" - PTS-2 Extension #{$extension->id} (main_supervisor stage): Requires manual evaluation by Main Supervisor.");
            }

            // 2. DPGC Stage for PTS-2 Extension
            if ($extension->current_stage === 'dpgc' && is_null($extension->dpgc_submitted_at ?? null)) {
                $refTime = $extension->main_supervisor_submitted_at ?? $extension->created_at;
                $elapsed = $refTime ? (int) abs(now()->diffInMinutes($refTime)) : $minutes + 1;
                if ($elapsed >= $minutes) {
                    $extension->dpgc_recommendation = true;
                    $extension->dpgc_confidential_remark = 'Auto-recommended';
                    $this->setIfColumnExists($extension, 'dpgc_submitted_at', now());
                    $extension->current_stage = 'hod';
                    $updated = true;
                    $this->info("PTS-2 Extension #{$extension->id}: DPGC auto-recommended after {$elapsed}m (threshold {$minutes}m). Stage advanced to hod.");
                } else {
                    $this->line(" - PTS-2 Extension #{$extension->id} (dpgc stage): Elapsed {$elapsed}m / Required {$minutes}m.");
                }
            }

            if ($updated) {
                $extension->save();
            }
        }
    }
}
