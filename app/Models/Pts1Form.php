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
            'seminar_date' => 'date',
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

    /**
     * Get human-readable role label for the authority who reverted the form.
     */
    public function getRevertedByRoleLabel(): string
    {
        if (str_starts_with($this->reverted_by_role ?? '', 'co_supervisor')) {
            return 'Co-Supervisor';
        }
        if (str_starts_with($this->reverted_by_role ?? '', 'pspc_member')) {
            return 'PSPC Member';
        }

        return match ($this->reverted_by_role) {
            'main_supervisor' => 'Main Supervisor',
            'dpgc' => 'DPGC Convenor',
            'hod' => 'Head of Department (HOD)',
            'section_officer' => 'Academic Section Officer',
            'doaa' => 'Dean of Academic Affairs (DOAA)',
            default => $this->reverted_by_role ?? 'Academic Authority',
        };
    }    /**
     * Get the reversion comment left by the reverting authority.
     */
    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }
}
