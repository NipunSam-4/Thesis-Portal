<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pts3Form extends Model
{
    use HasFactory;

    protected $table = 'pts3_forms';

    protected $guarded = [];

    protected $casts = [
        'main_supervisor_recommendation' => 'boolean',
        'main_supervisor_submitted_at' => 'datetime',

        'co_supervisor_1_recommendation' => 'boolean',
        'co_supervisor_1_submitted_at' => 'datetime',
        'co_supervisor_2_recommendation' => 'boolean',
        'co_supervisor_2_submitted_at' => 'datetime',
        'co_supervisor_3_recommendation' => 'boolean',
        'co_supervisor_3_submitted_at' => 'datetime',
        'co_supervisor_4_recommendation' => 'boolean',
        'co_supervisor_4_submitted_at' => 'datetime',
        'co_supervisor_5_recommendation' => 'boolean',
        'co_supervisor_5_submitted_at' => 'datetime',
        'co_supervisor_6_recommendation' => 'boolean',
        'co_supervisor_6_submitted_at' => 'datetime',
        'co_supervisor_7_recommendation' => 'boolean',
        'co_supervisor_7_submitted_at' => 'datetime',
        'co_supervisor_8_recommendation' => 'boolean',
        'co_supervisor_8_submitted_at' => 'datetime',
        'co_supervisor_9_recommendation' => 'boolean',
        'co_supervisor_9_submitted_at' => 'datetime',
        'co_supervisor_10_recommendation' => 'boolean',
        'co_supervisor_10_submitted_at' => 'datetime',
        'co_supervisors_submitted_at' => 'datetime',

        'dpgc_recommendation' => 'boolean',
        'dpgc_submitted_at' => 'datetime',

        'hod_recommendation' => 'boolean',
        'hod_submitted_at' => 'datetime',

        'academic_office_is_verified' => 'boolean',
        'academic_office_submitted_at' => 'datetime',

        'doaa_is_verified' => 'boolean',
        'doaa_submitted_at' => 'datetime',

        'senate_chairperson_approval' => 'boolean',
        'senate_chairperson_submitted_at' => 'datetime',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function student()
    {
        return $this->thesis->student();
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(Pts3Examiner::class);
    }

    public function indianExaminers(): HasMany
    {
        return $this->hasMany(Pts3Examiner::class)->where('examiner_type', 'indian');
    }

    public function internationalExaminers(): HasMany
    {
        return $this->hasMany(Pts3Examiner::class)->where('examiner_type', 'international');
    }

    public function oebMembers(): HasMany
    {
        return $this->hasMany(Pts3OebMember::class);
    }

    public function mainSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_supervisor_id');
    }

    public function getCoSupervisor(int $index): ?User
    {
        $id = $this->{"co_supervisor_{$index}_id"};
        return $id ? User::find($id) : null;
    }

    /**
     * Get all co-supervisors assigned to this form keyed by slot index (1 to 10).
     * Executes in 1 single database query.
     */
    public function getCoSupervisors(): array
    {
        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($id = $this->{"co_supervisor_{$i}_id"}) {
                $ids[$i] = $id;
            }
        }

        if (empty($ids)) {
            return [];
        }

        $users = User::with('externalSupervisorProfile')->whereIn('id', array_values($ids))->get()->keyBy('id');

        $result = [];
        foreach ($ids as $slot => $userId) {
            if (isset($users[$userId])) {
                $result[$slot] = $users[$userId];
            }
        }

        return $result;
    }

    public function dpgcUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dpgc_user_id');
    }

    public function hodUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function academicOfficeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_office_user_id');
    }

    public function doaaUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doaa_user_id');
    }

    public function senateChairpersonUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'senate_chairperson_user_id');
    }

    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    // Accessor for human-readable status label mapped from Thesis.
    // Usage in Blade: {{ $pts3Form->status_label }}
    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    public function getRevertedByRoleLabel(): string
    {
        return Thesis::getRevertedByRoleLabel($this);
    }

    // Numerical rank for role in PTS-3 workflow hierarchy.
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }

        return match ($role) {
            'main_supervisor' => 1,
            'co_supervisors', 'co_supervisor', 'external_supervisor' => 2,
            'dpgc' => 3,
            'hod' => 4,
            'academic_office' => 5,
            'doaa', 'adoaa' => 6,
            'senate_chairperson' => 7,
            default => 999,
        };
    }

    // Determine access status for in-progress PTS-3 submission: 'allowed', 'pending_endorsement', 'not_reached', or 'unauthorized'
    public function getUserSubmissionAccessStatus(?User $user): string
    {
        if (!$user || $user->isStudent()) {
            return 'unauthorized';
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;
        $stageRank = self::getRoleRank($this->current_stage);

        // 1. Main Supervisor (Rank 1)
        if ($student?->isMainSupervisor($user) || $this->main_supervisor_id === $user->id) {
            if ($stageRank < 1) return 'not_reached';
            if ($stageRank === 1) {
                return ($this->main_supervisor_submitted_at && $this->main_supervisor_recommendation !== null) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 2. Co-Supervisors (Internal and External) (Rank 2)
        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"co_supervisor_{$i}_id"} == $user->id) {
                if ($stageRank < 2) return 'not_reached';
                if ($stageRank === 2) {
                    $subCol = "co_supervisor_{$i}_submitted_at";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    return ($this->$subCol && !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
                }
                return 'allowed';
            }
        }

        // 3. DPGC (Rank 3)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) return 'not_reached';
            if ($stageRank === 3) {
                return ($this->dpgc_submitted_at && !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 4. HOD (Rank 4)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->hod_submitted_at && !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 5. Academic Office (Rank 5)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 5) return 'not_reached';
            if ($stageRank === 5) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 6. DOAA / ADoAA / Acting DOAA / Vested DOAA (Rank 6)
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 6) return 'not_reached';
            if ($stageRank === 6) {
                return ($this->doaa_submitted_at && !is_null($this->doaa_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 7. Senate Chairperson (Rank 7)
        if ($user->isSenateChairperson()) {
            if ($stageRank < 7) return 'not_reached';
            if ($stageRank === 7) {
                return ($this->senate_chairperson_submitted_at && !is_null($this->senate_chairperson_approval)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        return 'unauthorized';
    }

    // Check if user is authorized to view this PTS-3 form in its current state
    public function canUserView(?User $user): bool
    {
        if (!$user || $user->isStudent()) {
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

        if ($student && ($student->isSupervisor($user) || $this->main_supervisor_id === $user->id)) {
            return true;
        }

        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                return true;
            }
        }

        if ($user->isDpgc() || $user->isHod()) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson()) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email)) {
            return true;
        }

        return false;
    }

    // Check if user is currently authorized to review/endorse this PTS-3 form
    public function canUserReview(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user || $this->status !== 'reverted') {
            return false;
        }

        if ($user->isStudent()) {
            return false;
        }

        $reverterRole = $this->reverted_by_role;
        if (!$reverterRole) {
            return false;
        }

        $reverterRank = self::getRoleRank($reverterRole);

        if ($user->isFaculty()) {
            $isMain = ($this->main_supervisor_id === $user->id);
            $isCo = false;
            for ($i = 1; $i <= 10; $i++) {
                if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                    $isCo = true;
                    break;
                }
            }

            if ($isMain && $reverterRank >= 1) return true;
            if ($isCo && $reverterRank >= 2) return true;
            if ($user->isDpgc() && $reverterRank >= 3) return true;
            if ($user->isHod() && $reverterRank >= 4) return true;
        }

        if ($user->isExternalSupervisor()) {
            $isCo = false;
            for ($i = 1; $i <= 10; $i++) {
                if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                    $isCo = true;
                    break;
                }
            }
            if ($isCo && $reverterRank >= 2) return true;
        }

        if ($user->isAcademicOffice() && $reverterRank >= 5) return true;
        if (($user->isDoaa() || $user->isAdoaa()) && $reverterRank >= 6) return true;
        if ($user->isSenateChairperson() && $reverterRank >= 7) return true;

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email) && $reverterRank >= 6) {
            return true;
        }

        return false;
    }

    /**
     * Get submitted timeline steps for submission-timeline-modal component.
     */
    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
