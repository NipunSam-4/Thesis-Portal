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
        'reversion_comment',

        'main_supervisor_recommendation',
        'main_supervisor_confidential_remark',
        'main_supervisor_submitted_at',
        'main_supervisor_draft_synopsis_report_doc_path',
        'main_supervisor_publication_list_doc_path',
        'main_supervisor_publication_approval_doc_path',
        'main_supervisor_min_time_approval_doc_path',

        'co_supervisor_1_id', 'co_supervisor_1_recommendation', 'co_supervisor_1_confidential_remark',
        'co_supervisor_2_id', 'co_supervisor_2_recommendation', 'co_supervisor_2_confidential_remark',
        'co_supervisor_3_id', 'co_supervisor_3_recommendation', 'co_supervisor_3_confidential_remark',
        'co_supervisor_4_id', 'co_supervisor_4_recommendation', 'co_supervisor_4_confidential_remark',
        'co_supervisor_5_id', 'co_supervisor_5_recommendation', 'co_supervisor_5_confidential_remark',
        'co_supervisor_6_id', 'co_supervisor_6_recommendation', 'co_supervisor_6_confidential_remark',
        'co_supervisor_7_id', 'co_supervisor_7_recommendation', 'co_supervisor_7_confidential_remark',
        'co_supervisor_8_id', 'co_supervisor_8_recommendation', 'co_supervisor_8_confidential_remark',
        'co_supervisor_9_id', 'co_supervisor_9_recommendation', 'co_supervisor_9_confidential_remark',
        'co_supervisor_10_id', 'co_supervisor_10_recommendation', 'co_supervisor_10_confidential_remark',
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

        'dpgc_student_comment', 'dpgc_recommendation', 'dpgc_confidential_remark',
        'hod_student_comment', 'hod_recommendation', 'hod_confidential_remark',
        'section_officer_student_comment', 'section_officer_recommendation', 'section_officer_confidential_remark',
        'doaa_student_comment', 'doaa_approval', 'doaa_confidential_remark', 'pts1_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'seminar_date' => 'date:d-m-Y',
            'publication_norm_fulfillment' => 'boolean',
            'special_approval_publication' => 'boolean',
            'min_time_req_fulfilled' => 'boolean',
            'special_approval_min_time' => 'boolean',
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
            'section_officer_recommendation' => 'boolean',
            'doaa_approval' => 'boolean',

            'main_supervisor_submitted_at' => 'datetime',
            'co_supervisors_submitted_at' => 'datetime',
            'pspc_members_submitted_at' => 'datetime',
            'pts1_submitted_at' => 'datetime',
        ];
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function coSupervisor1(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_1_id'); }
    public function coSupervisor2(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_2_id'); }
    public function coSupervisor3(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_3_id'); }
    public function coSupervisor4(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_4_id'); }
    public function coSupervisor5(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_5_id'); }
    public function coSupervisor6(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_6_id'); }
    public function coSupervisor7(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_7_id'); }
    public function coSupervisor8(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_8_id'); }
    public function coSupervisor9(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_9_id'); }
    public function coSupervisor10(): BelongsTo { return $this->belongsTo(User::class, 'co_supervisor_10_id'); }

    public function pspcMember1(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_1_id'); }
    public function pspcMember2(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_2_id'); }
    public function pspcMember3(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_3_id'); }
    public function pspcMember4(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_4_id'); }
    public function pspcMember5(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_5_id'); }
    public function pspcMember6(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_6_id'); }
    public function pspcMember7(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_7_id'); }
    public function pspcMember8(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_8_id'); }
    public function pspcMember9(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_9_id'); }
    public function pspcMember10(): BelongsTo { return $this->belongsTo(User::class, 'pspc_member_10_id'); }

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
            'dpgc' => 4,
            'hod' => 5,
            'section_officer' => 6,
            'doaa' => 7,
            default => 999,
        };
    }

    // Allowed only for the reverting authority, authorities prior to them in rank, and the student.
    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // If form is not reverted, default viewing rules apply
        if ($this->status !== 'reverted') {
            return true;
        }

        $revertingRank = self::getRoleRank($this->reverted_by_role);

        // Student owner can always view their reverted form
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
        if ($user->isDoaa()) {
            $userRanks[] = self::getRoleRank('doaa');
        }

        if (empty($userRanks)) {
            return false;
        }

        $minUserRank = min($userRanks);
        return $minUserRank <= $revertingRank;
    }

    // Get human-readable role label for the authority who reverted the form, including user name.
    public function getRevertedByRoleLabel(): string
    {
        return \App\Http\Controllers\ThesisController::getRevertedByRoleLabel($this);
    }

    // Get the reversion comment left by the reverting authority.
    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Main Supervisor document path helpers (returns null if not populated).
    public function getEffectiveDraftSynopsisPath(): ?string
    {
        return $this->main_supervisor_draft_synopsis_report_doc_path;
    }

    public function getEffectivePublicationListPath(): ?string
    {
        return $this->main_supervisor_publication_list_doc_path;
    }

    public function getEffectivePublicationApprovalPath(): ?string
    {
        return $this->main_supervisor_publication_approval_doc_path;
    }

    public function getEffectiveMinTimeApprovalPath(): ?string
    {
        return $this->main_supervisor_min_time_approval_doc_path;
    }

    // Accessor for human-readable stage label mapped from ThesisController.
    public function getStageLabelAttribute(): string
    {
        return \App\Http\Controllers\ThesisController::getStageLabel($this->current_stage);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        $timeline = [];

        if ($this->created_at) {
            $timeline[] = [
                'role' => 'Student Submission',
                'name' => $this->thesis?->student?->user?->name ?? 'Student',
                'submitted_at' => $this->created_at,
            ];
        }

        if ($this->main_supervisor_submitted_at || $this->main_supervisor_recommendation !== null) {
            $mainSup = $this->thesis?->student?->mainSupervisors?->first();
            $timeline[] = [
                'role' => 'Main Supervisor',
                'name' => $mainSup?->name ?? 'Main Supervisor',
                'submitted_at' => $this->main_supervisor_submitted_at,
            ];
        }

        for ($i = 1; $i <= 10; $i++) {
            $submittedAt = $this->{"co_supervisor_{$i}_submitted_at"} ?? ($this->co_supervisors_submitted_at && $this->{"co_supervisor_{$i}_recommendation"} !== null ? $this->co_supervisors_submitted_at : null);
            if ($submittedAt || $this->{"co_supervisor_{$i}_recommendation"} !== null) {
                $coSup = $this->{"coSupervisor{$i}"};
                $timeline[] = [
                    'role' => 'Co-Supervisor',
                    'name' => $coSup?->name ?? "Co-Supervisor {$i}",
                    'submitted_at' => $submittedAt,
                ];
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            $submittedAt = $this->{"pspc_member_{$i}_submitted_at"} ?? ($this->pspc_members_submitted_at && $this->{"pspc_member_{$i}_recommendation"} !== null ? $this->pspc_members_submitted_at : null);
            if ($submittedAt || $this->{"pspc_member_{$i}_recommendation"} !== null) {
                $pspc = $this->{"pspcMember{$i}"};
                $timeline[] = [
                    'role' => 'PSPC Member',
                    'name' => $pspc?->name ?? "PSPC Member {$i}",
                    'submitted_at' => $submittedAt ?? $this->updated_at,
                ];
            }
        }

        if ($this->dpgc_submitted_at || $this->dpgc_recommendation !== null) {
            $timeline[] = [
                'role' => 'DPGC Convenor',
                'name' => 'DPGC Convenor',
                'submitted_at' => $this->dpgc_submitted_at,
            ];
        }

        if ($this->hod_submitted_at || $this->hod_recommendation !== null) {
            $timeline[] = [
                'role' => 'Head of Department',
                'name' => 'HOD',
                'submitted_at' => $this->hod_submitted_at,
            ];
        }

        if ($this->section_officer_submitted_at || $this->section_officer_recommendation !== null) {
            $timeline[] = [
                'role' => 'Academic Office (SO)',
                'name' => 'Section Officer',
                'submitted_at' => $this->section_officer_submitted_at,
            ];
        }

        if ($this->doaa_submitted_at || $this->doaa_approval !== null) {
            $timeline[] = [
                'role' => 'Dean of Academic Affairs',
                'name' => 'DOAA',
                'submitted_at' => $this->doaa_submitted_at ?? $this->pts1_submitted_at,
            ];
        }

        if ($this->status === 'reverted' && ($this->reverted_by_role || $this->reversion_comment)) {
            $timeline[] = [
                'role' => \App\Http\Controllers\ThesisController::getStageLabel($this->reverted_by_role),
                'name' => 'Reverting Authority',
                'submitted_at' => $this->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        return $timeline;
    }
}
