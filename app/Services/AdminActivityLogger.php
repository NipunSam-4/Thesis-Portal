<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminActivityLogger
{
    /**
     * Log an admin action to storage/logs/admin_activity.log
     *
     * @param string $action Action description (e.g. 'Created Department')
     * @param string|null $target Target entity identifier (e.g. 'User #12 (johndoe@iiti.ac.in)')
     * @param array $changes Map of field changes or details (e.g. ['name' => ['old' => 'A', 'new' => 'B']])
     */
    public static function log(string $action, ?string $target = null, array $changes = []): void
    {
        try {
            $admin = auth('admin')->user() ?? auth()->user();
            $adminInfo = $admin ? "{$admin->name} ({$admin->email}, ID: {$admin->id})" : "System/Guest";
            $timestamp = now()->format('Y-m-d H:i:s');

            $changeStrings = [];
            foreach ($changes as $field => $val) {
                if (is_array($val) && (array_key_exists('old', $val) || array_key_exists('new', $val))) {
                    $old = isset($val['old']) ? (is_bool($val['old']) ? ($val['old'] ? 'Active/True' : 'Inactive/False') : $val['old']) : 'None';
                    $new = isset($val['new']) ? (is_bool($val['new']) ? ($val['new'] ? 'Active/True' : 'Inactive/False') : $val['new']) : 'None';
                    $changeStrings[] = "{$field}: '{$old}' -> '{$new}'";
                } elseif (is_scalar($val)) {
                    $changeStrings[] = "{$field}: '{$val}'";
                }
            }

            $diffText = !empty($changeStrings) ? ' | Changes: [' . implode(', ', $changeStrings) . ']' : '';
            $targetText = $target ? " | Target: {$target}" : '';

            $logLine = "[{$timestamp}] ADMIN_ACTION | Admin: {$adminInfo} | Action: {$action}{$targetText}{$diffText}" . PHP_EOL;

            $logPath = storage_path('logs/admin_activity.log');
            File::append($logPath, $logLine);
        } catch (\Throwable $e) {
            Log::error('Failed to write to admin_activity.log: ' . $e->getMessage());
        }
    }
}
