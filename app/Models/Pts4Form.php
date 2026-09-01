<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts4Form extends Model
{
    use HasFactory;

    protected $table = 'pts4_forms';

    protected $fillable = [
        'thesis_id',
        'current_stage',
        'status',
        'acting_doaa_email',
        'vested_doaa_email',
        'approved_by_authority',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    // Accessor for human-readable stage label mapped from Thesis.
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    public static function getRoleRank(?string $role): int
    {
        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'co_supervisors' => 2,
            'dpgc' => 3,
            'hod' => 4,
            'academic_office' => 5,
            'doaa' => 6,
            'completed' => 7,
            default => 99,
        };
    }

    public function getUserSubmissionAccessStatus(?User $user): string
    {
        if (!$user) {
            return 'unauthorized';
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;

        if ($student && (int)$user->id === (int)$student->user_id) {
            return 'allowed';
        }

        $stageRank = self::getRoleRank($this->current_stage);
        $statuses = [];

        // 1. Main Supervisor (Rank 1)
        if ($student && $student->isMainSupervisor($user)) {
            if ($stageRank < 1) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 1) {
                $statuses[] = $this->main_supervisor_submitted_at ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 2. Co-Supervisor (Rank 2)
        $coSupSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($this->$col == $user->id) {
                $coSupSlot = $i;
                break;
            }
        }
        if ($coSupSlot !== null) {
            if ($stageRank < 2) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 2) {
                $subCol = "co_supervisor_{$coSupSlot}_submitted_at";
                $recCol = "co_supervisor_{$coSupSlot}_recommendation";
                $statuses[] = ($this->$subCol || !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 3. DPGC (Rank 3)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 3) {
                $statuses[] = ($this->dpgc_submitted_at || !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 4. HOD (Rank 4)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 4) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 4) {
                $statuses[] = ($this->hod_submitted_at || !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 5. Academic Office (Rank 5)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 5) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 5) {
                $statuses[] = ($this->academic_office_submitted_at || !is_null($this->academic_office_verified)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 6. DOAA / Global Authorities (Rank 6)
        if ($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 6) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 6) {
                $statuses[] = ($this->doaa_submitted_at || !is_null($this->doaa_approval)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        if (empty($statuses)) {
            return 'unauthorized';
        }

        if (in_array('pending_endorsement', $statuses)) {
            return 'pending_endorsement';
        }
        if (in_array('allowed', $statuses)) {
            return 'allowed';
        }
        return 'not_reached';
    }

    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;

        if ($student && (int)$user->id === (int)$student->user_id) {
            return true;
        }

        $revertedByRank = self::getRoleRank($this->reverted_by_role ?? 'doaa');

        if ($student && $student->isMainSupervisor($user) && $revertedByRank >= 1) {
            return true;
        }

        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($this->$col == $user->id && $revertedByRank >= 2) {
                return true;
            }
        }

        if ($user->isDpgc() && $revertedByRank >= 3) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isHod() && $revertedByRank >= 4) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isAcademicOffice() && $revertedByRank >= 5) {
            return true;
        }

        if (($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic()) && $revertedByRank >= 6) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email) && $revertedByRank >= 6) {
            return true;
        }

        return false;
    }

    public function canUserView(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->status === 'reverted') {
            return $this->canUserViewRevertedForm($user);
        }

        if ($this->status === 'in_progress') {
            $access = $this->getUserSubmissionAccessStatus($user);
            return $access === 'allowed' || $access === 'pending_endorsement';
        }

        // For completed forms (approved / rejected)
        $thesis = $this->thesis;
        $student = $thesis?->student;

        if ($student && (int)$user->id === (int)$student->user_id) {
            return true;
        }

        if ($student && $student->isSupervisor($user)) {
            return true;
        }

        if ($user->isDpgc() || $user->isHod()) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic()) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email)) {
            return true;
        }

        return false;
    }

    public function canUserEvaluate(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }
}
