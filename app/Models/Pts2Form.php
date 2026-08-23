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
        'acting_doaa_email',
        'vested_doaa_email',
        'approved_by_authority',
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

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by_id');
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
            default => 999,
        };
    }

    // Check if a given user is allowed to view the reverted form.
    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->status !== 'reverted') {
            return true;
        }

        $revertingRank = self::getRoleRank($this->reverted_by_role);

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

    public function getRevertedByRoleLabel(): string
    {
        return \App\Http\Controllers\ThesisController::getRevertedByRoleLabel($this);
    }

    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Effective Declaration Helpers (Prefers Main Supervisor values if submitted, falls back to student's initial submission)
    public function getEffectiveCertPrimaFacieCase(): bool
    {
        return $this->main_supervisor_cert_prima_facie_case !== null 
            ? (bool)$this->main_supervisor_cert_prima_facie_case 
            : (bool)$this->cert_prima_facie_case;
    }

    public function getEffectiveCertNoPriorDegreeSubmission(): bool
    {
        return $this->main_supervisor_cert_no_prior_degree_submission !== null 
            ? (bool)$this->main_supervisor_cert_no_prior_degree_submission 
            : (bool)$this->cert_no_prior_degree_submission;
    }

    public function getEffectiveCollaborativeWorkStatus(): bool
    {
        return $this->main_supervisor_collaborative_work_status !== null 
            ? (bool)$this->main_supervisor_collaborative_work_status 
            : (bool)$this->collaborative_work_status;
    }

    public function getEffectiveCollaborativeWorkDetails(): ?string
    {
        $details = null;
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

    // Accessor for human-readable stage label mapped from ThesisController.
    // Usage in Blade: {{ $pts2Form->stage_label }}
    public function getStageLabelAttribute(): string
    {
        if ($this->status === 'rejected' || $this->current_stage === 'rejected') {
            return 'Rejected';
        }

        return \App\Http\Controllers\ThesisController::getStageLabel($this->current_stage);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        $timeline = [];
        $student = $this->thesis?->student;
        $deptId = $student?->department_id;

        $soUser = User::whereIn('role', ['section_officer', 'academic_office'])->first();
        $doaaUser = User::whereIn('role', ['doaa', 'adoaa'])->first();

        // 1. Student Submission
        if ($this->created_at) {
            $timeline[] = [
                'role' => 'Student Submission',
                'name' => $student?->user?->name ?? 'Student',
                'submitted_at' => $this->created_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        }

        // 2. Main Supervisor
        $mainSup = $student?->mainSupervisors?->first() ?? $student?->mainSupervisor;
        $mainSupSubmitted = $this->main_supervisor_submitted_at || $this->main_supervisor_recommendation !== null;
        if ($mainSupSubmitted) {
            $timeline[] = [
                'role' => 'Main Supervisor',
                'name' => $mainSup?->name ?? 'Main Supervisor',
                'submitted_at' => $this->main_supervisor_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'main_supervisor') {
            $timeline[] = [
                'role' => 'Main Supervisor',
                'name' => $mainSup?->name ?? 'Main Supervisor',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 3. Co-Supervisors
        $coSupervisors = $student?->coSupervisors ?? collect();
        $hasCoSupervisors = $coSupervisors->count() > 0 || $this->co_supervisor_1_id;
        if ($hasCoSupervisors) {
            $maxCo = max(1, $coSupervisors->count());
            for ($i = 1; $i <= 10; $i++) {
                $coSup = $this->{"coSupervisor{$i}"} ?? $coSupervisors->get($i - 1);
                if (!$coSup && $i > $maxCo) break;

                $submittedAt = $this->{"co_supervisor_{$i}_submitted_at"} ?? ($this->co_supervisors_submitted_at && $this->{"co_supervisor_{$i}_recommendation"} !== null ? $this->co_supervisors_submitted_at : null);
                $isSubmitted = $submittedAt || $this->{"co_supervisor_{$i}_recommendation"} !== null;

                if ($isSubmitted) {
                    $timeline[] = [
                        'role' => 'Co-Supervisor',
                        'name' => $coSup?->name ?? "Co-Supervisor {$i}",
                        'submitted_at' => $submittedAt,
                        'status_type' => 'submitted',
                        'status_label' => '✓ Submitted',
                    ];
                } elseif ($this->status === 'in_progress' && $this->current_stage === 'co_supervisors' && $coSup) {
                    $timeline[] = [
                        'role' => 'Co-Supervisor',
                        'name' => $coSup->name ?? "Co-Supervisor {$i}",
                        'submitted_at' => null,
                        'status_type' => 'pending',
                        'status_label' => '⏳ Pending',
                    ];
                }
            }
        }

        // 4. Academic Office
        $aoSubmitted = $this->academic_office_submitted_at || $this->academic_office_is_verified !== null;
        if ($aoSubmitted) {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'submitted_at' => $this->academic_office_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'academic_office') {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 5. DOAA
        $doaaSubmitted = $this->doaa_submitted_at || $this->doaa_approval !== null;
        if ($doaaSubmitted) {
            $isApproved = $this->status === 'approved' || $this->doaa_approval == true;
            $isRejected = $this->status === 'rejected' || $this->doaa_approval === false;
            $timeline[] = [
                'role' => 'Dean of Academic Affairs',
                'name' => 'DOAA',
                'submitted_at' => $this->doaa_submitted_at,
                'status_type' => $isRejected ? 'rejected' : ($isApproved ? 'approved' : 'submitted'),
                'status_label' => $isRejected ? '❌ Rejected' : ($isApproved ? '✓ Approved' : '✓ Submitted'),
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'doaa') {
            $timeline[] = [
                'role' => 'Dean of Academic Affairs',
                'name' => 'DOAA',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 6. Reverted Event (if reverted)
        if ($this->status === 'reverted' && ($this->reverted_by_role || $this->reversion_comment)) {
            $timeline[] = [
                'role' => \App\Http\Controllers\ThesisController::getStageLabel($this->reverted_by_role),
                'name' => $this->revertedBy?->name ?? 'Reverting Authority',
                'submitted_at' => $this->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        return $timeline;
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
}
