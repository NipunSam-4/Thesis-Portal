<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts2Extension extends Model
{
    use HasFactory;

    protected $table = 'pts2_extensions';

    protected $fillable = [
        'thesis_id',
        'reason_for_extension',
        'extended_until_date',
        'status',
        'current_stage',
        'reverted_by_role',
        'reverted_by_id',
        'reversion_comment',
        'main_supervisor_recommendation',
        'main_supervisor_confidential_remark',
        'main_supervisor_submitted_at',
        'dpgc_recommendation',
        'dpgc_confidential_remark',
        'dpgc_submitted_at',
        'hod_recommendation',
        'hod_confidential_remark',
        'hod_submitted_at',
        'academic_office_recommendation',
        'academic_office_confidential_remark',
        'academic_office_submitted_at',
        'doaa_recommendation',
        'doaa_confidential_remark',
        'doaa_student_comment',
        'doaa_submitted_at',
        'approved_extended_until_date',
        'main_supervisor_id',
        'dpgc_user_id',
        'hod_user_id',
        'academic_office_user_id',
        'doaa_user_id',
        'acting_doaa_email',
        'vested_doaa_email',
        'approved_by_id',
    ];

    protected function casts(): array
    {
        return [
            'extended_until_date' => 'date',
            'approved_extended_until_date' => 'date',
            'main_supervisor_recommendation' => 'boolean',
            'main_supervisor_submitted_at' => 'datetime',
            'dpgc_recommendation' => 'boolean',
            'dpgc_submitted_at' => 'datetime',
            'hod_recommendation' => 'boolean',
            'hod_submitted_at' => 'datetime',
            'academic_office_recommendation' => 'boolean',
            'academic_office_submitted_at' => 'datetime',
            'doaa_recommendation' => 'boolean',
            'doaa_submitted_at' => 'datetime',
        ];
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by_id');
    }

    // Get numerical rank for role in PTS-2 Extension workflow hierarchy.
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }

        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'dpgc' => 2,
            'hod' => 3,
            'academic_office' => 4,
            'doaa' => 5,
            default => 999,
        };
    }

    // Check if viewing role can view a prior role's remark based on workflow rank.
    public static function canViewPriorRemark(string $viewerRole, string $targetRole): bool
    {
        $viewerRank = self::getRoleRank($viewerRole);
        $targetRank = self::getRoleRank($targetRole);

        return $viewerRank >= $targetRank;
    }

    // Check if a given user is allowed to view the reverted PTS-2 Extension form.
    // Allowed only for the reverting authority, authorities prior to them in rank, and the student.
    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user || $this->status !== 'reverted') {
            return false;
        }

        $revertingRank = self::getRoleRank($this->reverted_by_role);

        // Student owner can always view their reverted form
        $student = $this->thesis?->student;
        if ($student && (int)$user->id === (int)$student->user_id) {
            return true;
        }

        $userRanks = [];
        if ($student && $student->isMainSupervisor($user)) {
            $userRanks[] = self::getRoleRank('main_supervisor');
        }

        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            $userRanks[] = self::getRoleRank('dpgc');
        }
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            $userRanks[] = self::getRoleRank('hod');
        }
        if ($user->isAcademicOffice()) {
            $userRanks[] = self::getRoleRank('academic_office');
        }
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            $userRanks[] = self::getRoleRank('doaa');
        }

        if (empty($userRanks)) {
            return false;
        }

        $minUserRank = min($userRanks);
        return $minUserRank <= $revertingRank;
    }

    // Determine access status for in-progress submitted extension: 'allowed', 'pending_endorsement', 'not_reached', or 'unauthorized'
    public function getUserSubmissionAccessStatus(?User $user): string
    {
        if (!$user) {
            return 'unauthorized';
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;

        // Student owner can always view their in-progress submission
        if ($student && (int)$user->id === (int)$student->user_id) {
            return 'allowed';
        }

        $stageRank = self::getRoleRank($this->current_stage);

        // 1. Main Supervisor (Rank 1)
        if ($student?->isMainSupervisor($user)) {
            if ($stageRank < 1) return 'not_reached';
            if ($stageRank === 1) {
                return ($this->main_supervisor_submitted_at && $this->main_supervisor_recommendation !== null) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 2. DPGC (Rank 2)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 2) return 'not_reached';
            if ($stageRank === 2) {
                return ($this->dpgc_submitted_at && !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 3. HOD (Rank 3)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) return 'not_reached';
            if ($stageRank === 3) {
                return ($this->hod_submitted_at && !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 4. Academic Office (Rank 4)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 5. DOAA (Rank 5)
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 5) return 'not_reached';
            if ($stageRank === 5) {
                return ($this->doaa_submitted_at && !is_null($this->doaa_approval)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        return 'unauthorized';
    }

    // Check if user is authorized to view this PTS-2 extension form in its current state
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

        if ($student && ($student->isSupervisor($user) || $student->isPspcMember($user))) {
            return true;
        }

        if ($user->isDpgc() || $user->isHod()) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isAcademicOffice() || $user->isDoaa()) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email)) {
            return true;
        }

        return false;
    }

    // Check if user is currently authorized to review/endorse this PTS-2 extension form
    public function canUserReview(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    // Format reverted by role label.
    public function getRevertedByRoleLabel(): string
    {
        return Thesis::getRevertedByRoleLabel($this);
    }
    
    // Get the reversion comment left by the reverting authority.
    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Accessor for human-readable stage label mapped from Thesis.
    // Usage in Blade: {{ $pts2Extension->stage_label }}
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    // Accessor for human-readable status label mapped from Thesis.
    // Usage in Blade: {{ $pts2Extension->status_label }}
    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function mainSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_supervisor_id');
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
