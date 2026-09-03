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
        'thesis_title',
        'thesis_doc_path',
        'current_stage',
        'status',
        'reverted_by_role',
        'reverted_by_id',
        'reversion_comment',
        'hindi_name',
        'current_address',
        'alternate_email',
        'alternate_phone_number',
        'alternate_phone_country_code',
        'alternate_phone_iso2',
        'main_supervisor_thesis_title',
        'main_supervisor_thesis_doc_path',
        'main_supervisor_recommendation',
        'main_supervisor_student_comment',
        'main_supervisor_confidential_remark',
        'main_supervisor_submitted_at',
        'co_supervisor_1_id', 'co_supervisor_1_recommendation', 'co_supervisor_1_student_comment', 'co_supervisor_1_confidential_remark', 'co_supervisor_1_submitted_at',
        'co_supervisor_2_id', 'co_supervisor_2_recommendation', 'co_supervisor_2_student_comment', 'co_supervisor_2_confidential_remark', 'co_supervisor_2_submitted_at',
        'co_supervisor_3_id', 'co_supervisor_3_recommendation', 'co_supervisor_3_student_comment', 'co_supervisor_3_confidential_remark', 'co_supervisor_3_submitted_at',
        'co_supervisor_4_id', 'co_supervisor_4_recommendation', 'co_supervisor_4_student_comment', 'co_supervisor_4_confidential_remark', 'co_supervisor_4_submitted_at',
        'co_supervisor_5_id', 'co_supervisor_5_recommendation', 'co_supervisor_5_student_comment', 'co_supervisor_5_confidential_remark', 'co_supervisor_5_submitted_at',
        'co_supervisor_6_id', 'co_supervisor_6_recommendation', 'co_supervisor_6_student_comment', 'co_supervisor_6_confidential_remark', 'co_supervisor_6_submitted_at',
        'co_supervisor_7_id', 'co_supervisor_7_recommendation', 'co_supervisor_7_student_comment', 'co_supervisor_7_confidential_remark', 'co_supervisor_7_submitted_at',
        'co_supervisor_8_id', 'co_supervisor_8_recommendation', 'co_supervisor_8_student_comment', 'co_supervisor_8_confidential_remark', 'co_supervisor_8_submitted_at',
        'co_supervisor_9_id', 'co_supervisor_9_recommendation', 'co_supervisor_9_student_comment', 'co_supervisor_9_confidential_remark', 'co_supervisor_9_submitted_at',
        'co_supervisor_10_id', 'co_supervisor_10_recommendation', 'co_supervisor_10_student_comment', 'co_supervisor_10_confidential_remark', 'co_supervisor_10_submitted_at',
        'co_supervisors_submitted_at',
        'academic_office_is_verified',
        'academic_office_verification_remark',
        'academic_office_confidential_remark',
        'academic_office_submitted_at',
        'dr_approval',
        'dr_student_comment',
        'dr_confidential_remark',
        'dr_submitted_at',
        'main_supervisor_id',
        'academic_office_user_id',
        'dr_user_id',
        'approved_by_id',
    ];

    protected function casts(): array
    {
        return [
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
            'academic_office_is_verified' => 'boolean',
            'academic_office_submitted_at' => 'datetime',
            'dr_approval' => 'boolean',
            'dr_submitted_at' => 'datetime',
        ];
    }

    // Effective Accessors & Helpers for Main Supervisor Updates
    public function getEffectiveThesisTitleAttribute(): ?string
    {
        return $this->main_supervisor_thesis_title ?: ($this->thesis_title ?: $this->thesis?->title);
    }

    public function getEffectiveThesisDocPathAttribute(): ?string
    {
        return $this->main_supervisor_thesis_doc_path ?: $this->thesis_doc_path;
    }

    public function getEffectiveThesisDocPath(): ?string
    {
        return $this->main_supervisor_thesis_doc_path ?: $this->thesis_doc_path;
    }

    public function getEffectiveThesisDocField(): string
    {
        return $this->main_supervisor_thesis_doc_path ? 'main_supervisor_thesis_doc_path' : 'thesis_doc_path';
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by_id');
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

        /**
     * Get numerical rank for role in PTS-4 workflow hierarchy.
     */
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }

        if (str_starts_with($role, 'co_supervisor_')) {
            return 2;
        }

        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'co_supervisors' => 2,
            'academic_office' => 3,
            'dr' => 4,
            'completed' => 5,
            default => 999,
        };
    }

    /**
     * Check if the form has passed a given stage.
     */
    public function hasPassedStage(string $stage): bool
    {
        $currentRank = self::getRoleRank($this->current_stage);
        $targetRank = self::getRoleRank($stage);

        if ($currentRank > $targetRank) {
            return true;
        }

        return match ($stage) {
            'main_supervisor' => !is_null($this->main_supervisor_submitted_at) && !is_null($this->main_supervisor_recommendation),
            'co_supervisors'  => !is_null($this->co_supervisors_submitted_at),
            'academic_office' => !is_null($this->academic_office_is_verified) && !is_null($this->academic_office_submitted_at),
            'dr'              => !is_null($this->dr_approval) && !is_null($this->dr_submitted_at),
            default           => false,
        };
    }

    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user || $this->status !== 'reverted') {
            return false;
        }

        if ($user->isStudent()) {
            return false;
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;
        $revertingRank = self::getRoleRank($this->reverted_by_role);

        $userRanks = [];
        if ($student) {
            if ($student->isMainSupervisor($user)) {
                $userRanks[] = self::getRoleRank('main_supervisor');
            }
            if ($student->isCoSupervisor($user)) {
                $userRanks[] = self::getRoleRank('co_supervisors');
            }
        }

        if ($user->isAcademicOffice()) {
            $userRanks[] = self::getRoleRank('academic_office');
        }
        if ($user->isDr()) {
            $userRanks[] = self::getRoleRank('dr');
        }

        if (empty($userRanks)) {
            return false;
        }

        $minUserRank = min($userRanks);
        return $minUserRank <= $revertingRank;
    }

    // Determine access status for in-progress submitted form: 'allowed', 'pending_endorsement', 'not_reached', or 'unauthorized'.
    // Supports dual-role faculty: a user may simultaneously be main supervisor and co-supervisor.
    // The current stage rank is used as the tiebreaker to decide which role is active right now.
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

        // --- Collect all role slots this user occupies ---
        $isMainSup = (bool)($student && $student->isMainSupervisor($user));

        $coSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"co_supervisor_{$i}_id"} == $user->id) {
                $coSlot = $i;
                break;
            }
        }

        // --- Resolve access based on which role is active at the current stage ---
        if ($isMainSup || $coSlot !== null) {
            // Rank 1 — main supervisor stage
            if ($stageRank === 1 && $isMainSup) {
                return ($this->main_supervisor_submitted_at && $this->main_supervisor_recommendation !== null)
                    ? 'allowed' : 'pending_endorsement';
            }

            // Rank 2 — co-supervisor stage
            if ($stageRank === 2 && $coSlot !== null) {
                $subCol = "co_supervisor_{$coSlot}_submitted_at";
                $recCol = "co_supervisor_{$coSlot}_recommendation";
                return ($this->$subCol && !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
            }

            // Stage hasn't reached any of this user's roles yet
            $minOwnedRank = min(
                $isMainSup       ? 1 : PHP_INT_MAX,
                $coSlot !== null ? 2 : PHP_INT_MAX
            );
            if ($stageRank < $minOwnedRank) return 'not_reached';

            // Stage has passed all of this user's roles
            return 'allowed';
        }

        // 3. Academic Office (Rank 3)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 3) return 'not_reached';
            if ($stageRank === 3) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 4. DR (Rank 4)
        if ($user->isDr()) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->dr_submitted_at && !is_null($this->dr_approval)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        return 'unauthorized';
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

        if ($student && ($student->isSupervisor($user) || $student->isPspcMember($user))) {
            return true;
        }

        if ($user->isAcademicOffice() || $user->isDr()) {
            return true;
        }

        return false;
    }

    public function canUserReview(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    public function canMainSupervisorEdit(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress' || $this->current_stage !== 'main_supervisor') {
            return false;
        }

        $thesis = $this->thesis;
        return (bool)($thesis && $thesis->student && $thesis->student->isMainSupervisor($user));
    }

    public function canUserRevert(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        if ($this->current_stage === 'academic_office' || $user->isAcademicOffice()) {
            return false;
        }

        return $this->canUserReview($user) || $this->canMainSupervisorEdit($user);
    }

    public static function canViewPriorRemark(string $viewerRole, string $targetRole): bool
    {
        $viewerRank = self::getRoleRank($viewerRole);
        $targetRank = self::getRoleRank($targetRole);

        return $viewerRank >= $targetRank;
    }

    public function getRevertedByRoleLabel(): string
    {
        return Thesis::getRevertedByRoleLabel($this);
    }

    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function getFormattedAlternatePhoneNumber(): string
    {
        if (!$this->alternate_phone_number) {
            return 'N/A';
        }
        $code = $this->alternate_phone_country_code ?: '+91';
        return trim("{$code} {$this->alternate_phone_number}");
    }

    public function mainSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_supervisor_id');
    }

    public function academicOfficeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_office_user_id');
    }

    public function drUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dr_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
