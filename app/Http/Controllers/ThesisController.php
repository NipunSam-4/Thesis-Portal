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
                    $user = $form->thesis?->student?->allCoSupervisors()?->get($idx - 1);
                }
            }
            $student = $form->thesis?->student;
            $roleTitle = ($student && $user) ? $student->getSupervisorRoleTitle($user) : 'Co-Supervisor';
            $coName = $user?->name ?? $student?->allCoSupervisors()?->first()?->name;
            $inst = ($user?->isExternalSupervisor() && $user->externalSupervisorProfile?->affiliated_institute)
                ? ' - ' . $user->externalSupervisorProfile->affiliated_institute
                : '';
            return $roleTitle . ($coName ? " ({$coName}{$inst})" : '');
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

    // Get the standardized timeline item for a reverted form.
    // @param mixed $form Any form model (Pts1Form, Pts2Form, Pts2Extension, etc.)
    // @return array|null
    public static function getRevertedTimelineItem($form): ?array
    {
        if (!$form || $form->status !== 'reverted' || (!$form->reverted_by_role && !$form->reversion_comment)) {
            return null;
        }

        $student = $form->thesis?->student;
        $role = $form->reverted_by_role ?? '';

        $revertingUser = $form->revertedBy ?? null;
        if (!$revertingUser && !empty($form->reverted_by_id)) {
            $revertingUser = User::find($form->reverted_by_id);
        }

        if (!$revertingUser && str_starts_with($role, 'co_supervisor_')) {
            if (preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $form->$col ?? null;
                $revertingUser = $userId ? User::find($userId) : $student?->allCoSupervisors()?->get($idx - 1);
            }
        } elseif (!$revertingUser && $role === 'main_supervisor') {
            $revertingUser = $student?->mainSupervisors?->first();
        } elseif (!$revertingUser && str_starts_with($role, 'pspc_member_')) {
            if (preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $form->$col ?? null;
                $revertingUser = $userId ? User::find($userId) : $student?->pspcMembers?->get($idx - 1);
            }
        }

        $revertRole = self::getStageLabel($role);
        if ($student && $revertingUser && str_starts_with($role, 'co_supervisor')) {
            $revertRole = $student->getSupervisorRoleTitle($revertingUser);
        }

        $revertName = $revertingUser?->name ?? 'Reverting Authority';
        $revertInstitute = ($revertingUser?->isExternalSupervisor() && $revertingUser->externalSupervisorProfile?->affiliated_institute)
            ? $revertingUser->externalSupervisorProfile->affiliated_institute
            : null;

        return [
            'role' => $revertRole,
            'name' => $revertName,
            'institute' => $revertInstitute,
            'submitted_at' => $form->updated_at,
            'status_type' => 'reverted',
            'status_label' => '⚠️ Reverted',
        ];
    }
}
