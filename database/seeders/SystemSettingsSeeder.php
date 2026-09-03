<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'pts2_normal_deadline_days',
                'value' => '16',
                'label' => 'PTS-2 Initial Submission Deadline (Days)',
                'description' => 'Initial number of days from Open Seminar date to submit PTS-2 form without extension.',
            ],
            [
                'key' => 'pts2_max_extension_days',
                'value' => '30',
                'label' => 'PTS-2 Maximum Extension Limit (Days)',
                'description' => 'Maximum allowed days from Open Seminar date for PTS-2 extension applications.',
            ],
            [
                'key' => 'pts4_normal_deadline_days',
                'value' => '30',
                'label' => 'PTS-4 Initial Submission Deadline (Days)',
                'description' => 'Initial number of days from Open Seminar date to submit PTS-4 form without extension.',
            ],
            [
                'key' => 'pts4_max_extension_days',
                'value' => '60',
                'label' => 'PTS-4 Maximum Extension Limit (Days)',
                'description' => 'Maximum allowed days from Open Seminar date for PTS-4 extension applications.',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                ]
            );
        }

        SystemSetting::clearCache();
    }
}
