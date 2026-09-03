<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts1Form extends Model
{
    use HasFactory;

    protected $table = 'pts1_forms';

    protected $fillable = [
        'thesis_id',
        'thesis_title',
        'seminar_date',
        'seminar_time',
        'seminar_venue',
        'meeting_link',
        'publication_norm_fulfillment',
        'special_approval_publication',
        'publication_approval_doc_path',
        'min_time_req_fulfilled',
        'special_approval_min_time',
        'min_time_approval_doc_path',
        'draft_synopsis_report_doc_path',
        'publication_list_doc_path',
        'work_status',
        'main_supervisor_student_comment',
        'current_stage',
        'status',
        'reverted_by_role',
        'reverted_by_id',
        'reversion_comment',

        'main_supervisor_thesis_title',
        'main_supervisor_seminar_date',
        'main_supervisor_seminar_time',
        'main_supervisor_seminar_venue',
        'main_supervisor_meeting_link',
        'main_supervisor_publication_norm_fulfillment',
        'main_supervisor_special_approval_publication',
        'main_supervisor_min_time_req_fulfilled',
        'main_supervisor_special_approval_min_time',
        'main_supervisor_date_confirmation',

        'main_supervisor_recommendation',
        'main_supervisor_confidential_remark',
        'main_supervisor_submitted_at',
        'main_supervisor_draft_synopsis_report_doc_path',
        'main_supervisor_publication_list_doc_path',
        'main_supervisor_publication_approval_doc_path',
        'main_supervisor_min_time_approval_doc_path',

        'co_supervisor_1_id', 'co_supervisor_1_recommendation', 'co_supervisor_1_confidential_remark', 'co_supervisor_1_submitted_at',
        'co_supervisor_2_id', 'co_supervisor_2_recommendation', 'co_supervisor_2_confidential_remark', 'co_supervisor_2_submitted_at',
        'co_supervisor_3_id', 'co_supervisor_3_recommendation', 'co_supervisor_3_confidential_remark', 'co_supervisor_3_submitted_at',
        'co_supervisor_4_id', 'co_supervisor_4_recommendation', 'co_supervisor_4_confidential_remark', 'co_supervisor_4_submitted_at',
        'co_supervisor_5_id', 'co_supervisor_5_recommendation', 'co_supervisor_5_confidential_remark', 'co_supervisor_5_submitted_at',
        'co_supervisor_6_id', 'co_supervisor_6_recommendation', 'co_supervisor_6_confidential_remark', 'co_supervisor_6_submitted_at',
        'co_supervisor_7_id', 'co_supervisor_7_recommendation', 'co_supervisor_7_confidential_remark', 'co_supervisor_7_submitted_at',
        'co_supervisor_8_id', 'co_supervisor_8_recommendation', 'co_supervisor_8_confidential_remark', 'co_supervisor_8_submitted_at',
        'co_supervisor_9_id', 'co_supervisor_9_recommendation', 'co_supervisor_9_confidential_remark', 'co_supervisor_9_submitted_at',
        'co_supervisor_10_id', 'co_supervisor_10_recommendation', 'co_supervisor_10_confidential_remark', 'co_supervisor_10_submitted_at',
        'co_supervisors_submitted_at',

        'pspc_member_1_id', 'pspc_member_1_recommendation', 'pspc_member_1_confidential_remark',
        'pspc_member_2_id', 'pspc_member_2_recommendation', 'pspc_member_2_confidential_remark',
        'pspc_member_3_id', 'pspc_member_3_recommendation', 'pspc_member_3_confidential_remark',
        'pspc_member_4_id', 'pspc_member_4_recommendation', 'pspc_member_4_confidential_remark',
        'pspc_member_5_id', 'pspc_member_5_recommendation', 'pspc_member_5_confidential_remark',
        'pspc_member_6_id', 'pspc_member_6_recommendation', 'pspc_member_6_confidential_remark',
        'pspc_member_7_id', 'pspc_member_7_recommendation', 'pspc_member_7_confidential_remark',
        'pspc_member_8_id', 'pspc_member_8_recommendation', 'pspc_member_8_confidential_remark',
        'pspc_member_9_id', 'pspc_member_9_recommendation', 'pspc_member_9_confidential_remark',
        'pspc_member_10_id', 'pspc_member_10_recommendation', 'pspc_member_10_confidential_remark',
        'pspc_members_submitted_at',

        'dpgc_student_comment', 'dpgc_recommendation', 'dpgc_confidential_remark', 'dpgc_submitted_at',
        'hod_student_comment', 'hod_recommendation', 'hod_confidential_remark', 'hod_submitted_at',
        'academic_office_is_verified', 'academic_office_confidential_remark', 'academic_office_submitted_at',
        'doaa_student_comment', 'doaa_approval', 'doaa_confidential_remark', 'doaa_submitted_at',
        'main_supervisor_id', 'dpgc_user_id', 'hod_user_id', 'academic_office_user_id', 'doaa_user_id',
        'acting_doaa_email', 'vested_doaa_email', 'approved_by_id',
    ];

    protected function casts(): array
    {
        return [
            'seminar_date' => 'date:d-m-Y',
            'publication_norm_fulfillment' => 'boolean',
            'special_approval_publication' => 'boolean',
            'min_time_req_fulfilled' => 'boolean',
            'special_approval_min_time' => 'boolean',

            'main_supervisor_seminar_date' => 'date:d-m-Y',
            'main_supervisor_publication_norm_fulfillment' => 'boolean',
            'main_supervisor_special_approval_publication' => 'boolean',
            'main_supervisor_min_time_req_fulfilled' => 'boolean',
            'main_supervisor_special_approval_min_time' => 'boolean',
            'main_supervisor_date_confirmation' => 'date:d-m-Y',
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

            'pspc_member_1_recommendation' => 'boolean',
            'pspc_member_2_recommendation' => 'boolean',
            'pspc_member_3_recommendation' => 'boolean',
            'pspc_member_4_recommendation' => 'boolean',
            'pspc_member_5_recommendation' => 'boolean',
            'pspc_member_6_recommendation' => 'boolean',
            'pspc_member_7_recommendation' => 'boolean',
            'pspc_member_8_recommendation' => 'boolean',
            'pspc_member_9_recommendation' => 'boolean',
            'pspc_member_10_recommendation' => 'boolean',

            'dpgc_recommendation' => 'boolean',
            'hod_recommendation' => 'boolean',
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

            'pspc_member_1_submitted_at' => 'datetime',
            'pspc_member_2_submitted_at' => 'datetime',
            'pspc_member_3_submitted_at' => 'datetime',
            'pspc_member_4_submitted_at' => 'datetime',
            'pspc_member_5_submitted_at' => 'datetime',
            'pspc_member_6_submitted_at' => 'datetime',
            'pspc_member_7_submitted_at' => 'datetime',
            'pspc_member_8_submitted_at' => 'datetime',
            'pspc_member_9_submitted_at' => 'datetime',
            'pspc_member_10_submitted_at' => 'datetime',
            'pspc_members_submitted_at' => 'datetime',
            'dpgc_submitted_at' => 'datetime',
            'hod_submitted_at' => 'datetime',
            'academic_office_submitted_at' => 'datetime',
            'doaa_submitted_at' => 'datetime',
        ];
    }

    // Effective Accessors for Main Supervisor Updates
    public function getEffectiveThesisTitleAttribute(): ?string
    {
        return $this->main_supervisor_thesis_title ?: ($this->thesis_title ?: $this->thesis?->title);
    }

    public function getEffectiveSeminarDateAttribute()
    {
        return $this->main_supervisor_seminar_date ?? $this->seminar_date;
    }

    public function getEffectiveSeminarTimeAttribute(): ?string
    {
        return $this->main_supervisor_seminar_time ?? $this->seminar_time;
    }

    public function getEffectiveSeminarVenueAttribute(): ?string
    {
        return $this->main_supervisor_seminar_venue ?? $this->seminar_venue;
    }

    public function getEffectiveMeetingLinkAttribute(): ?string
    {
        return $this->main_supervisor_meeting_link ?? $this->meeting_link;
    }

    public function getEffectivePublicationNormFulfillmentAttribute(): bool
    {
        return (bool)($this->main_supervisor_publication_norm_fulfillment ?? $this->publication_norm_fulfillment);
    }

    public function getEffectiveSpecialApprovalPublicationAttribute(): bool
    {
        return (bool)($this->main_supervisor_special_approval_publication ?? $this->special_approval_publication);
    }

    public function getEffectiveMinTimeReqFulfilledAttribute(): bool
    {
        return (bool)($this->main_supervisor_min_time_req_fulfilled ?? $this->min_time_req_fulfilled);
    }

    public function getEffectiveSpecialApprovalMinTimeAttribute(): bool
    {
        return (bool)($this->main_supervisor_special_approval_min_time ?? $this->special_approval_min_time);
    }

    public function getEffectiveDraftSynopsisDocPathAttribute(): ?string
    {
        return $this->main_supervisor_draft_synopsis_report_doc_path ?? $this->draft_synopsis_report_doc_path;
    }

    public function getEffectiveDraftSynopsisPath(): ?string
    {
        return $this->effective_draft_synopsis_doc_path;
    }

    public function getEffectiveDraftSynopsisField(): string
    {
        return $this->main_supervisor_draft_synopsis_report_doc_path ? 'main_supervisor_draft_synopsis_report_doc_path' : 'draft_synopsis_report_doc_path';
    }

    public function getEffectivePublicationListDocPathAttribute(): ?string
    {
        return $this->main_supervisor_publication_list_doc_path ?? $this->publication_list_doc_path;
    }

    public function getEffectivePublicationListPath(): ?string
    {
        return $this->effective_publication_list_doc_path;
    }

    public function getEffectivePublicationListField(): string
    {
        return $this->main_supervisor_publication_list_doc_path ? 'main_supervisor_publication_list_doc_path' : 'publication_list_doc_path';
    }

    public function getEffectivePublicationApprovalDocPathAttribute(): ?string
    {
        return $this->main_supervisor_publication_approval_doc_path ?? $this->publication_approval_doc_path;
    }

    public function getEffectivePublicationApprovalPath(): ?string
    {
        return $this->effective_publication_approval_doc_path;
    }

    public function getEffectivePublicationApprovalField(): string
    {
        return $this->main_supervisor_publication_approval_doc_path ? 'main_supervisor_publication_approval_doc_path' : 'publication_approval_doc_path';
    }

    public function getEffectiveMinTimeApprovalDocPathAttribute(): ?string
    {
        return $this->main_supervisor_min_time_approval_doc_path ?? $this->min_time_approval_doc_path;
    }

    public function getEffectiveMinTimeApprovalPath(): ?string
    {
        return $this->effective_min_time_approval_doc_path;
    }

    public function getEffectiveMinTimeApprovalField(): string
    {
        return $this->main_supervisor_min_time_approval_doc_path ? 'main_supervisor_min_time_approval_doc_path' : 'min_time_approval_doc_path';
    }

    public function getEffectiveDateConfirmationAttribute()
    {
        return $this->main_supervisor_date_confirmation ?? $this->thesis?->student?->date_confirmation;
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
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

    /**
     * Get all PSPC members assigned to this form keyed by slot index (1 to 10).
     * Executes in 1 single database query.
     */
    public function getPspcMembers(): array
    {
        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($id = $this->{"pspc_member_{$i}_id"}) {
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
        if (str_starts_with($role, 'pspc_member')) {
            return 3;
        }

        return match ($role) {
            'student' => 0,
            'main_supervisor' => 1,
            'co_supervisors' => 2,
            'pspc_members' => 3,
            'dpgc' => 4,
            'hod' => 5,
            'academic_office' => 6,
            'doaa' => 7,
            'completed' => 8,
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
            'pspc_members'    => !is_null($this->pspc_members_submitted_at),
            'dpgc'            => !is_null($this->dpgc_submitted_at) && !is_null($this->dpgc_recommendation),
            'hod'             => !is_null($this->hod_submitted_at) && !is_null($this->hod_recommendation),
            'academic_office' => !is_null($this->academic_office_submitted_at) && !is_null($this->academic_office_is_verified),
            'doaa'            => !is_null($this->doaa_submitted_at) && !is_null($this->doaa_approval),
            default           => false,
        };
    }

    // Allowed only for the reverting authority, authorities prior to them in rank, and the student.
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
    // Supports dual/triple-role faculty: a user may simultaneously be main supervisor, co-supervisor, and/or PSPC member.
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

        $pspcSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"pspc_member_{$i}_id"} == $user->id) {
                $pspcSlot = $i;
                break;
            }
        }

        // --- Resolve access based on which role is active at the current stage ---
        if ($isMainSup || $coSlot !== null || $pspcSlot !== null) {
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

            // Rank 3 — pspc stage
            if ($stageRank === 3 && $pspcSlot !== null) {
                $subCol = "pspc_member_{$pspcSlot}_submitted_at";
                $recCol = "pspc_member_{$pspcSlot}_recommendation";
                return ($this->$subCol && !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
            }

            // Stage hasn't reached any of this user's roles yet
            $minOwnedRank = min(
                $isMainSup  ? 1   : PHP_INT_MAX,
                $coSlot !== null  ? 2   : PHP_INT_MAX,
                $pspcSlot !== null ? 3  : PHP_INT_MAX
            );
            if ($stageRank < $minOwnedRank) return 'not_reached';

            // Stage has passed all of this user's roles
            return 'allowed';
        }

        // 5. DPGC (Rank 4)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->dpgc_submitted_at && !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 6. HOD (Rank 5)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 5) return 'not_reached';
            if ($stageRank === 5) {
                return ($this->hod_submitted_at && !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 7. Academic Office (Rank 6)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 6) return 'not_reached';
            if ($stageRank === 6) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 8. DOAA / Global Authorities (Rank 7)
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 7) return 'not_reached';
            if ($stageRank === 7) {
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

    // Get human-readable role label for the authority who reverted the form, including user name.
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
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    // Usage in Blade: {{ $pts1Form->status_label }}
    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by_id');
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
