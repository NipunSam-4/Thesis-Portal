<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ThesisController extends Controller
{
    // Single unified stage mapping for all forms across the entire portal.
    // Configure all stage label displays here in one single place.
    public static array $stageMappings = [
        'main_supervisor' => 'Main Supervisor',
        'co_supervisors'  => 'Co-Supervisors',
        'pspc_members'    => 'PSPC Members',
        'dpgc'            => 'DPGC',
        'hod'             => 'HOD',
        'section_officer'    => 'Section Officer',
        'academic_office'    => 'Academic Office',
        'adoaa'              => 'ADoAA',
        'doaa'            => 'DOAA',
        'completed'       => 'Approved',
        'rejected'        => 'Rejected',
        'reverted'        => 'Reverted',
    ];

    // Get the mapped stage label for a given stage code.
    // @param string|null $stage e.g. 'main_supervisor', 'dpgc', 'adoaa', etc.
    // @return string
    public static function getStageLabel(?string $stage): string
    {
        if (!$stage) {
            return 'N/A';
        }

        return self::$stageMappings[$stage] ?? ucwords(str_replace('_', ' ', $stage));
    }

    // Get human-readable role label for the authority who reverted the form, including user name.
    // @param mixed $form Any form model (Pts1Form, Pts2Form, Pts2Extension, etc.)
    // @return string
    public static function getRevertedByRoleLabel($form): string
    {
        if (!$form) {
            return 'Academic Authority';
        }

        $role = $form->reverted_by_role ?? '';
        if (!$role) {
            return 'Academic Authority';
        }

        $user = null;
        if (!empty($form->reverted_by_id)) {
            $user = User::find($form->reverted_by_id);
        }

        if ($role === 'main_supervisor') {
            $name = $user?->name ?? $form->thesis?->student?->mainSupervisors?->first()?->name;
            return 'Main Supervisor' . ($name ? " ({$name})" : '');
        }

        if (str_starts_with($role, 'co_supervisor')) {
            if (!$user && preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
                if (!$user) {
                    $user = $form->thesis?->student?->coSupervisors?->get($idx - 1);
                }
            }
            $coName = $user?->name ?? $form->thesis?->student?->coSupervisors?->first()?->name;
            return 'Co-Supervisor' . ($coName ? " ({$coName})" : '');
        }

        if (str_starts_with($role, 'pspc_member')) {
            if (!$user && preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
            }
            return 'PSPC Member' . ($user ? " ({$user->name})" : '');
        }

        $roleLabel = self::getStageLabel($role);
        return $roleLabel . ($user ? " ({$user->name})" : '');
    }
}
