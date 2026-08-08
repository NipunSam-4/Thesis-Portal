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
            'seminar_date' => 'date',
            'publication_norm_fulfillment' => 'boolean',
            'special_approval_publication' => 'boolean',
            'min_time_req_fulfilled' => 'boolean',
            'special_approval_min_time' => 'boolean',
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
     * Get human-readable role label for the authority who reverted the form.
     */
    public function getRevertedByRoleLabel(): string
    {
        return match ($this->reverted_by_role) {
            'main_supervisor' => 'Main Supervisor',
            'co_supervisor_1', 'co_supervisor_2', 'co_supervisor_3' => 'Co-Supervisor',
            'pspc_member_1', 'pspc_member_2', 'pspc_member_3' => 'PSPC Member',
            'dpgc' => 'DPGC Convenor',
            'hod' => 'Head of Department (HOD)',
            'section_officer' => 'Academic Section Officer',
            'doaa' => 'Dean of Academic Affairs (DOAA)',
            default => $this->reverted_by_role ?? 'Academic Authority',
        };
    }

    /**
     * Resolve the exact reversion comment string from the matching authority column.
     */
    public function getReversionComment(): ?string
    {
        return match ($this->reverted_by_role) {
            'main_supervisor' => $this->main_supervisor_confidential_remark,
            'co_supervisor_1' => $this->co_supervisor_1_confidential_remark,
            'co_supervisor_2' => $this->co_supervisor_2_confidential_remark,
            'co_supervisor_3' => $this->co_supervisor_3_confidential_remark,
            'pspc_member_1'   => $this->pspc_member_1_confidential_remark,
            'pspc_member_2'   => $this->pspc_member_2_confidential_remark,
            'pspc_member_3'   => $this->pspc_member_3_confidential_remark,
            'dpgc'            => $this->dpgc_confidential_remark,
            'hod'             => $this->hod_confidential_remark,
            'section_officer' => $this->section_officer_confidential_remark,
            'doaa'            => $this->doaa_confidential_remark,
            default           => null,
        };
    }
}
