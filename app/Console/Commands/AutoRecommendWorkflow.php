<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pts1Form;
use App\Models\Pts2Extension;

class AutoRecommendWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:auto-recommend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-recommend pending PTS-1 and PTS-2 Extension stages after 24 hours of inactivity from previous authority submission.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting 24-hour Auto-Recommendation workflow check...');

        $this->processPts1Forms();
        $this->processPts2Extensions();

        $this->info('Auto-Recommendation workflow check completed successfully.');
        return Command::SUCCESS;
    }

    /**
     * Process PTS-1 forms.
     */
    protected function processPts1Forms()
    {
        $forms = Pts1Form::where('status', 'in_progress')
            ->whereNotNull('main_supervisor_submitted_at')
            ->get();

        foreach ($forms as $pts1) {
            $updated = false;

            // 1. Co-Supervisors Stage
            if ($pts1->current_stage === 'co_supervisors' && $pts1->main_supervisor_submitted_at) {
                if (now()->diffInHours($pts1->main_supervisor_submitted_at) >= 24) {
                    for ($i = 1; $i <= 10; $i++) {
                        $coSupId = $pts1->{"co_supervisor_{$i}_id"};
                        if ($coSupId && is_null($pts1->{"co_supervisor_{$i}_submitted_at"})) {
                            $pts1->{"co_supervisor_{$i}_recommendation"} = true;
                            $pts1->{"co_supervisor_{$i}_confidential_remark"} = 'Auto-recommended';
                            $pts1->{"co_supervisor_{$i}_submitted_at"} = now();
                            $updated = true;
                        }
                    }
                    $pts1->co_supervisors_submitted_at = now();

                    // Check if PSPC members exist
                    $hasPspc = false;
                    for ($i = 1; $i <= 10; $i++) {
                        if ($pts1->{"pspc_member_{$i}_id"}) {
                            $hasPspc = true;
                            break;
                        }
                    }

                    $pts1->current_stage = $hasPspc ? 'pspc_members' : 'dpgc';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: Co-Supervisors auto-recommended after 24h. Stage advanced to {$pts1->current_stage}.");
                }
            }

            // 2. PSPC Committee Members Stage
            if ($pts1->current_stage === 'pspc_members') {
                $prevTimestamp = $pts1->co_supervisors_submitted_at ?? $pts1->main_supervisor_submitted_at;
                if ($prevTimestamp && now()->diffInHours($prevTimestamp) >= 24) {
                    for ($i = 1; $i <= 10; $i++) {
                        $pspcId = $pts1->{"pspc_member_{$i}_id"};
                        if ($pspcId && is_null($pts1->{"pspc_member_{$i}_submitted_at"})) {
                            $pts1->{"pspc_member_{$i}_recommendation"} = true;
                            $pts1->{"pspc_member_{$i}_confidential_remark"} = 'Auto-recommended';
                            $pts1->{"pspc_member_{$i}_submitted_at"} = now();
                            $updated = true;
                        }
                    }
                    $pts1->pspc_members_submitted_at = now();
                    $pts1->current_stage = 'dpgc';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: PSPC Members auto-recommended after 24h. Stage advanced to dpgc.");
                }
            }

            // 3. DPGC Convenor Stage
            if ($pts1->current_stage === 'dpgc' && is_null($pts1->dpgc_submitted_at)) {
                $prevTimestamp = $pts1->pspc_members_submitted_at ?? $pts1->co_supervisors_submitted_at ?? $pts1->main_supervisor_submitted_at;
                if ($prevTimestamp && now()->diffInHours($prevTimestamp) >= 24) {
                    $pts1->dpgc_recommendation = true;
                    $pts1->dpgc_confidential_remark = 'Auto-recommended';
                    $pts1->dpgc_submitted_at = now();
                    $pts1->current_stage = 'hod';
                    $updated = true;
                    $this->info("PTS-1 Form #{$pts1->id}: DPGC auto-recommended after 24h. Stage advanced to hod.");
                }
            }

            if ($updated) {
                $pts1->save();
            }
        }
    }

    /**
     * Process PTS-2 Extensions.
     */
    protected function processPts2Extensions()
    {
        $extensions = Pts2Extension::where('status', 'in_progress')
            ->where('current_stage', 'dpgc')
            ->whereNotNull('main_supervisor_submitted_at')
            ->whereNull('dpgc_submitted_at')
            ->get();

        foreach ($extensions as $extension) {
            if (now()->diffInHours($extension->main_supervisor_submitted_at) >= 24) {
                $extension->dpgc_recommendation = 1;
                $extension->dpgc_confidential_remark = 'Auto-recommended';
                $extension->dpgc_submitted_at = now();
                $extension->current_stage = 'hod';
                $extension->save();

                $this->info("PTS-2 Extension #{$extension->id}: DPGC auto-recommended after 24h. Stage advanced to hod.");
            }
        }
    }
}
