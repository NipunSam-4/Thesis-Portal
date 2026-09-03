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
        'thesis_title',
        'synopsis_report_doc_path',
        'current_stage',
        'status',
        'reverted_by_role',
        'reverted_by_id',
        'reversion_comment',

        // Student Details
        'course_credits_student',
        'date_of_submission',
        'current_address',
        'alternate_email',
        'recent_phone_number',
        'recent_phone_country_code',
        'recent_phone_iso2',
        'alternate_phone_number',
        'alternate_phone_country_code',
        'alternate_phone_iso2',
        'cert_prima_facie_case',
        'cert_no_prior_degree_submission',
        'collaborative_work_status',
        'collaborative_work_details',

        // 1. Main Supervisor
        'main_supervisor_thesis_title',
        'main_supervisor_synopsis_report_doc_path',
        'main_supervisor_cert_prima_facie_case',
        'main_supervisor_cert_no_prior_degree_submission',
        'main_supervisor_collaborative_work_status',
        'main_supervisor_collaborative_work_details',
        'main_supervisor_recommendation',
        'main_supervisor_student_comment',
        'main_supervisor_confidential_remark',
        'main_supervisor_submitted_at',

        // 2. Co-Supervisors (1 to 10)
        'co_supervisor_1_id',
        'co_supervisor_1_recommendation',
        'co_supervisor_1_student_comment',
        'co_supervisor_1_confidential_remark',
        'co_supervisor_1_submitted_at',

        'co_supervisor_2_id',
        'co_supervisor_2_recommendation',
        'co_supervisor_2_student_comment',
        'co_supervisor_2_confidential_remark',
        'co_supervisor_2_submitted_at',

        'co_supervisor_3_id',
        'co_supervisor_3_recommendation',
        'co_supervisor_3_student_comment',
        'co_supervisor_3_confidential_remark',
        'co_supervisor_3_submitted_at',

        'co_supervisor_4_id',
        'co_supervisor_4_recommendation',
        'co_supervisor_4_student_comment',
        'co_supervisor_4_confidential_remark',
        'co_supervisor_4_submitted_at',

        'co_supervisor_5_id',
        'co_supervisor_5_recommendation',
        'co_supervisor_5_student_comment',
        'co_supervisor_5_confidential_remark',
        'co_supervisor_5_submitted_at',

        'co_supervisor_6_id',
        'co_supervisor_6_recommendation',
        'co_supervisor_6_student_comment',
        'co_supervisor_6_confidential_remark',
        'co_supervisor_6_submitted_at',

        'co_supervisor_7_id',
        'co_supervisor_7_recommendation',
        'co_supervisor_7_student_comment',
        'co_supervisor_7_confidential_remark',
        'co_supervisor_7_submitted_at',

        'co_supervisor_8_id',
        'co_supervisor_8_recommendation',
        'co_supervisor_8_student_comment',
        'co_supervisor_8_confidential_remark',
        'co_supervisor_8_submitted_at',

        'co_supervisor_9_id',
        'co_supervisor_9_recommendation',
        'co_supervisor_9_student_comment',
        'co_supervisor_9_confidential_remark',
        'co_supervisor_9_submitted_at',

        'co_supervisor_10_id',
        'co_supervisor_10_recommendation',
        'co_supervisor_10_student_comment',
        'co_supervisor_10_confidential_remark',
        'co_supervisor_10_submitted_at',

        'co_supervisors_submitted_at',

        // 3. Academic Office
        'academic_office_is_verified',
        'academic_office_verification_remark',
        'academic_office_course_credits',
        'academic_office_submitted_at',

        // 4. DOAA
        'doaa_student_comment',
        'doaa_approval',
        'doaa_confidential_remark',
        'doaa_submitted_at',

        // Acting & Vested DOAA
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
            'date_of_submission' => 'date',
            'course_credits_student' => 'float',
            'academic_office_course_credits' => 'float',
            'cert_prima_facie_case' => 'boolean',
            'cert_no_prior_degree_submission' => 'boolean',
            'collaborative_work_status' => 'boolean',
            'main_supervisor_cert_prima_facie_case' => 'boolean',
            'main_supervisor_cert_no_prior_degree_submission' => 'boolean',
            'main_supervisor_collaborative_work_status' => 'boolean',
            'main_supervisor_recommendation' => 'boolean',
            'co_supervisor_1_recommendation' => 'boolean',
            'co_supervisor_2_recommendation' => 'boolean',
            'co_supervisor_3_recommendation' => 'boolean',
            'co_supervisor_4_recommendation' => 'boolean',
            'co_supervisor_5_recommendation' => 'boolean',
            'co_supervisor_6_recommendation' => 'boolean',
            'co_supervisor_7_recommendation' => 'boolean',
            'co_supervisor_8_recommendation' => 'boolean',
            'co_supervisor_9_recommendation' => 'boolean',
            'co_supervisor_10_recommendation' => 'boolean',
            'academic_office_is_verified' => 'boolean',
            'doaa_approval' => 'boolean',
            'main_supervisor_submitted_at' => 'datetime',
            'co_supervisor_1_submitted_at' => 'datetime',
            'co_supervisor_2_submitted_at' => 'datetime',
            'co_supervisor_3_submitted_at' => 'datetime',
            'co_supervisor_4_submitted_at' => 'datetime',
            'co_supervisor_5_submitted_at' => 'datetime',
            'co_supervisor_6_submitted_at' => 'datetime',
            'co_supervisor_7_submitted_at' => 'datetime',
            'co_supervisor_8_submitted_at' => 'datetime',
            'co_supervisor_9_submitted_at' => 'datetime',
            'co_supervisor_10_submitted_at' => 'datetime',
            'co_supervisors_submitted_at' => 'datetime',
            'academic_office_submitted_at' => 'datetime',
            'doaa_submitted_at' => 'datetime',
        ];
    }

    // Effective Accessors & Helpers for Main Supervisor Updates
    public function getEffectiveThesisTitleAttribute(): ?string
    {
        return $this->main_supervisor_thesis_title ?: ($this->thesis_title ?: $this->thesis?->title);
    }

    public function getEffectiveSynopsisDocPathAttribute(): ?string
    {
        return $this->main_supervisor_synopsis_report_doc_path ?? $this->synopsis_report_doc_path;
    }

    public function getEffectiveSynopsisPath(): ?string
    {
        return $this->effective_synopsis_doc_path;
    }

    public function getEffectiveSynopsisField(): string
    {
        return $this->main_supervisor_synopsis_report_doc_path ? 'main_supervisor_synopsis_report_doc_path' : 'synopsis_report_doc_path';
    }

    public function getEffectiveCertPrimaFacieCaseAttribute(): bool
    {
        return $this->main_supervisor_cert_prima_facie_case !== null 
            ? (bool)$this->main_supervisor_cert_prima_facie_case 
            : (bool)$this->cert_prima_facie_case;
    }

    public function getEffectiveCertPrimaFacieCase(): bool
    {
        return $this->effective_cert_prima_facie_case;
    }

    public function getEffectiveCertNoPriorDegreeSubmissionAttribute(): bool
    {
        return $this->main_supervisor_cert_no_prior_degree_submission !== null 
            ? (bool)$this->main_supervisor_cert_no_prior_degree_submission 
            : (bool)$this->cert_no_prior_degree_submission;
    }

    public function getEffectiveCertNoPriorDegreeSubmission(): bool
    {
        return $this->effective_cert_no_prior_degree_submission;
    }

    public function getEffectiveCollaborativeWorkStatusAttribute(): bool
    {
        return $this->main_supervisor_collaborative_work_status !== null 
            ? (bool)$this->main_supervisor_collaborative_work_status 
            : (bool)$this->collaborative_work_status;
    }

    public function getEffectiveCollaborativeWorkStatus(): bool
    {
        return $this->effective_collaborative_work_status;
    }

    public function getEffectiveCollaborativeWorkDetailsAttribute(): ?string
    {
        if ($this->main_supervisor_collaborative_work_status !== null) {
            $details = $this->main_supervisor_collaborative_work_status 
                ? $this->main_supervisor_collaborative_work_details 
                : null;
        } else {
            $details = $this->collaborative_work_status 
                ? $this->collaborative_work_details 
                : null;
        }

        return $details !== null ? trim($details) : null;
    }

    public function getEffectiveCollaborativeWorkDetails(): ?string
    {
        return $this->effective_collaborative_work_details;
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

        $users = User::whereIn('id', array_values($ids))->get()->keyBy('id');

        $result = [];
        foreach ($ids as $slot => $userId) {
            if (isset($users[$userId])) {
                $result[$slot] = $users[$userId];
            }
        }

        return $result;
    }

    // Get numerical rank for role in workflow hierarchy.
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }
        if (str_starts_with($role, 'co_supervisor')) {
            return 2;
        }

        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'co_supervisors' => 2,
            'academic_office' => 3,
            'doaa' => 4,
            'completed' => 5,
            default => 999,
        };
    }

    /**
     * Check if the form has passed or submitted a specific evaluation stage.
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
            'academic_office' => !is_null($this->academic_office_submitted_at) && !is_null($this->academic_office_is_verified),
            'doaa'            => !is_null($this->doaa_submitted_at) && !is_null($this->doaa_approval),
            default           => false,
        };
    }

    // Check if a given user is allowed to view the reverted form.
    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user || $this->status !== 'reverted') {
            return false;
        }

        // Students are not allowed to access authority reverted view
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
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            $userRanks[] = self::getRoleRank('doaa');
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

        // 1. Student owner can always view their in-progress submission
        if ($student && (int)$user->id === (int)$student->user_id) {
            return 'allowed';
        }

        $stageRank = self::getRoleRank($this->current_stage);

        // --- Collect all role slots this user occupies ---
        $isMainSup = (bool)$student?->isMainSupervisor($user);

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

        // 4. Academic Office (Rank 3)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 3) return 'not_reached';
            if ($stageRank === 3) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 5. DOAA (Rank 4)
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->doaa_submitted_at && !is_null($this->doaa_approval)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        return 'unauthorized';
    }

    // Check if user is authorized to view this form in its current state
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

    // Check if user is currently authorized to review/endorse this form
    public function canUserReview(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    // Check if user is the Main Supervisor allowed to edit the submission
    public function canMainSupervisorEdit(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress' || $this->current_stage !== 'main_supervisor') {
            return false;
        }

        $thesis = $this->thesis;
        return (bool)($thesis && $thesis->student && $thesis->student->isMainSupervisor($user));
    }

    // Check if user is authorized to revert this form back to student
    public function canUserRevert(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        if ($this->current_stage === 'academic_office' || $user->isAcademicOffice()) {
            return false;
        }

        return $this->canUserReview($user);
    }

    public function getRevertedByRoleLabel(): string
    {
        return Thesis::getRevertedByRoleLabel($this);
    }

    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Accessor for human-readable stage label mapped from Thesis.
    // Usage in Blade: {{ $pts2Form->stage_label }}
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function getFormattedRecentPhoneNumber(): string
    {
        if (!$this->recent_phone_number) {
            return 'N/A';
        }
        $code = $this->recent_phone_country_code ?: '+91';
        return trim("{$code} {$this->recent_phone_number}");
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
