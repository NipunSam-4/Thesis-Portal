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
        'additional_comments',
        'current_stage',
        'status',
        'reverted_by_role',
        'main_supervisor_endorsement',
        'main_supervisor_comment',
        'co_supervisor_1_id',
        'co_supervisor_1_endorsement',
        'co_supervisor_1_comment',
        'co_supervisor_2_id',
        'co_supervisor_2_endorsement',
        'co_supervisor_2_comment',
        'co_supervisor_3_id',
        'co_supervisor_3_endorsement',
        'co_supervisor_3_comment',
        'pspc_member_1_id',
        'pspc_member_1_endorsement',
        'pspc_member_1_comment',
        'pspc_member_2_id',
        'pspc_member_2_endorsement',
        'pspc_member_2_comment',
        'pspc_member_3_id',
        'pspc_member_3_endorsement',
        'pspc_member_3_comment',
        'dpgc_endorsement',
        'dpgc_comment',
        'hod_endorsement',
        'hod_comment',
        'section_officer_endorsement',
        'section_officer_comment',
        'doaa_endorsement',
        'doaa_comment',
    ];

    protected function casts(): array
    {
        return [
            'seminar_date' => 'date',
            'publication_norm_fulfillment' => 'boolean',
            'special_approval_publication' => 'boolean',
            'min_time_req_fulfilled' => 'boolean',
            'special_approval_min_time' => 'boolean',
            'main_supervisor_endorsement' => 'boolean',
            'co_supervisor_1_endorsement' => 'boolean',
            'co_supervisor_2_endorsement' => 'boolean',
            'co_supervisor_3_endorsement' => 'boolean',
            'pspc_member_1_endorsement' => 'boolean',
            'pspc_member_2_endorsement' => 'boolean',
            'pspc_member_3_endorsement' => 'boolean',
            'dpgc_endorsement' => 'boolean',
            'hod_endorsement' => 'boolean',
            'section_officer_endorsement' => 'boolean',
            'doaa_endorsement' => 'boolean',
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
            'main_supervisor' => $this->main_supervisor_comment,
            'co_supervisor_1' => $this->co_supervisor_1_comment,
            'co_supervisor_2' => $this->co_supervisor_2_comment,
            'co_supervisor_3' => $this->co_supervisor_3_comment,
            'pspc_member_1'   => $this->pspc_member_1_comment,
            'pspc_member_2'   => $this->pspc_member_2_comment,
            'pspc_member_3'   => $this->pspc_member_3_comment,
            'dpgc'            => $this->dpgc_comment,
            'hod'             => $this->hod_comment,
            'section_officer' => $this->section_officer_comment,
            'doaa'            => $this->doaa_comment,
            default           => null,
        };
    }
}
