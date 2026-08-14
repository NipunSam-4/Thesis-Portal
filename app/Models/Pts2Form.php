<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts2Form extends Model
{
    use HasFactory;

    protected $table = 'pts2_forms';

    protected $fillable = [
        'thesis_id',
        'remarks',
        'synopsis_report_doc_path',
        'current_stage',
        'status',
        'reverted_by_role',
        'reversion_comment',
        'main_supervisor_recommendation',
        'main_supervisor_confidential_remark',
        'co_supervisor_1_id',
        'co_supervisor_1_recommendation',
        'co_supervisor_1_confidential_remark',
        'co_supervisor_2_id',
        'co_supervisor_2_recommendation',
        'co_supervisor_2_confidential_remark',
        'co_supervisor_3_id',
        'co_supervisor_3_recommendation',
        'co_supervisor_3_confidential_remark',
        'pspc_member_1_id',
        'pspc_member_1_recommendation',
        'pspc_member_1_confidential_remark',
        'pspc_member_2_id',
        'pspc_member_2_recommendation',
        'pspc_member_2_confidential_remark',
        'pspc_member_3_id',
        'pspc_member_3_recommendation',
        'pspc_member_3_confidential_remark',
        'dpgc_recommendation',
        'dpgc_confidential_remark',
        'hod_recommendation',
        'hod_confidential_remark',
        'section_officer_recommendation',
        'section_officer_confidential_remark',
        'doaa_approval',
        'doaa_confidential_remark',
    ];

    protected function casts(): array
    {
        return [
            'main_supervisor_recommendation' => 'boolean',
            'co_supervisor_1_recommendation' => 'boolean',
            'co_supervisor_2_recommendation' => 'boolean',
            'co_supervisor_3_recommendation' => 'boolean',
            'pspc_member_1_recommendation' => 'boolean',
            'pspc_member_2_recommendation' => 'boolean',
            'pspc_member_3_recommendation' => 'boolean',
            'dpgc_recommendation' => 'boolean',
            'hod_recommendation' => 'boolean',
            'section_officer_recommendation' => 'boolean',
            'doaa_approval' => 'boolean',
        ];
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function coSupervisor1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_1_id');
    }

    public function coSupervisor2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_2_id');
    }

    public function coSupervisor3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_supervisor_3_id');
    }

    public function pspcMember1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pspc_member_1_id');
    }

    public function pspcMember2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pspc_member_2_id');
    }

    public function pspcMember3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pspc_member_3_id');
    }

    /**
     * Get numerical rank for role in workflow hierarchy.
     */
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }
        if (str_starts_with($role, 'co_supervisor')) {
            return 2;
        }
        if (str_starts_with($role, 'pspc_member')) {
            return 3;
        }

        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'dpgc' => 4,
            'hod' => 5,
            'section_officer' => 6,
            'doaa', 'adoaa' => 7,
            default => 999,
        };
    }

    /**
     * Check if a given user is allowed to view the reverted form.
     */
    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->status !== 'reverted') {
            return true;
        }

        $revertingRank = self::getRoleRank($this->reverted_by_role);

        $student = $this->thesis?->student;
        if ($student && (int)$user->id === (int)$student->user_id) {
            return true;
        }

        $userRanks = [];
        if ($student) {
            if ($student->isMainSupervisor($user)) {
                $userRanks[] = self::getRoleRank('main_supervisor');
            }
            if ($student->isCoSupervisor($user)) {
                $userRanks[] = self::getRoleRank('co_supervisor');
            }
            if ($student->isPspcMember($user)) {
                $userRanks[] = self::getRoleRank('pspc_member');
            }
        }

        if ($user->isDpgc()) {
            $userRanks[] = self::getRoleRank('dpgc');
        }
        if ($user->isHod()) {
            $userRanks[] = self::getRoleRank('hod');
        }
        if ($user->isSectionOfficer()) {
            $userRanks[] = self::getRoleRank('section_officer');
        }
        if ($user->isDoaa() || $user->isAdoaa()) {
            $userRanks[] = self::getRoleRank('doaa');
        }

        if (empty($userRanks)) {
            return false;
        }

        $minUserRank = min($userRanks);
        return $minUserRank <= $revertingRank;
    }

    public function getRevertedByRoleLabel(): string
    {
        $role = $this->reverted_by_role ?? '';

        if ($role === 'main_supervisor') {
            $name = $this->thesis?->student?->mainSupervisors?->first()?->name;
            return 'Main Supervisor' . ($name ? " ({$name})" : '');
        }

        if (str_starts_with($role, 'co_supervisor')) {
            if (preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $this->$col;
                $user = $userId ? User::find($userId) : null;
                if (!$user) {
                    $user = $this->thesis?->student?->coSupervisors?->get($idx - 1);
                }
                return 'Co-Supervisor' . ($user ? " ({$user->name})" : '');
            }
            $coName = $this->thesis?->student?->coSupervisors?->first()?->name;
            return 'Co-Supervisor' . ($coName ? " ({$coName})" : '');
        }

        if (str_starts_with($role, 'pspc_member')) {
            if (preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $this->$col;
                $user = $userId ? User::find($userId) : null;
                return 'PSPC Member' . ($user ? " ({$user->name})" : '');
            }
            return 'PSPC Member';
        }

        return match ($role) {
            'dpgc' => 'DPGC Convenor',
            'hod' => 'Head of Department (HOD)',
            'section_officer' => 'Section Officer (Academic)',
            'doaa' => 'Dean of Academic Affairs (DOAA)',
            default => $role ?: 'Academic Authority',
        };
    }

    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }
}
