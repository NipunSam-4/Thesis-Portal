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
        return $this->hasMany(Pts3Examiner::class)->where('type', 'indian');
    }

    public function internationalExaminers(): HasMany
    {
        return $this->hasMany(Pts3Examiner::class)->where('type', 'international');
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
        $stage = $this->current_stage;
        $stageRank = self::getRoleRank($stage);
        $statuses = [];

        // 1. Main Supervisor (Rank 1)
        if ($student?->isMainSupervisor($user) || $this->main_supervisor_id === $user->id) {
            if ($stageRank < 1) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 1) {
                $statuses[] = ($this->main_supervisor_submitted_at || $this->main_supervisor_recommendation !== null) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 2. Co-Supervisors (Internal and External) (Rank 2)
        $coSupSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($this->$col == $user->id) {
                $coSupSlot = $i;
                break;
            }
        }
        if ($coSupSlot !== null) {
            if ($stageRank < 2) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 2) {
                $subCol = "co_supervisor_{$coSupSlot}_submitted_at";
                $recCol = "co_supervisor_{$coSupSlot}_recommendation";
                $statuses[] = ($this->$subCol || !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 3. DPGC (Rank 3)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 3) {
                $statuses[] = ($this->dpgc_submitted_at || !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 4. HOD (Rank 4)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 4) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 4) {
                $statuses[] = ($this->hod_submitted_at || !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 5. Academic Office (Rank 5)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 5) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 5) {
                $statuses[] = ($this->academic_office_submitted_at || !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 6. DOAA / ADoAA / Acting DOAA / Vested DOAA (Rank 6)
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 6) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 6) {
                $statuses[] = ($this->doaa_submitted_at || !is_null($this->doaa_is_verified)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 7. Senate Chairperson (Rank 7)
        if ($user->isSenateChairperson()) {
            if ($stageRank < 7) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 7) {
                $statuses[] = ($this->senate_chairperson_submitted_at || !is_null($this->senate_chairperson_approval)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        if (empty($statuses)) {
            return 'unauthorized';
        }

        if (in_array('pending_endorsement', $statuses)) {
            return 'pending_endorsement';
        }
        if (in_array('allowed', $statuses)) {
            return 'allowed';
        }
        return 'not_reached';
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

    // Check if user is currently authorized to evaluate/endorse this PTS-3 form
    public function canUserEvaluate(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    public function canUserViewRevertedForm(User $user): bool
    {
        if ($user->isStudent()) {
            return false;
        }

        $reverterRole = $this->reverted_by_role;
        if (!$reverterRole) {
            return true;
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
        $timeline = [];
        $student = $this->thesis?->student;

        // 1. Main Supervisor
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

        // 3. Co-Supervisors & External Supervisors
        $coSupervisors = $student?->allCoSupervisors() ?? collect();
        $hasCoSupervisors = $coSupervisors->count() > 0 || $this->co_supervisor_1_id;
        if ($hasCoSupervisors) {
            $maxCo = max(1, $coSupervisors->count());
            for ($i = 1; $i <= 10; $i++) {
                $coSup = $this->getCoSupervisor($i) ?? $coSupervisors->get($i - 1);
                if (!$coSup && $i > $maxCo) break;

                $roleLabel = ($student && $coSup) ? $student->getSupervisorRoleTitle($coSup) : "Co-Supervisor {$i}";
                $nameLabel = $coSup?->name ?? "Co-Supervisor {$i}";
                $submittedAt = $this->{"co_supervisor_{$i}_submitted_at"}
                    ?? ($this->co_supervisors_submitted_at && $this->{"co_supervisor_{$i}_recommendation"} !== null ? $this->co_supervisors_submitted_at : null);
                $isSubmitted = $this->{"co_supervisor_{$i}_recommendation"} !== null || $submittedAt;

                if ($isSubmitted) {
                    $timeline[] = [
                        'role' => $roleLabel,
                        'name' => $nameLabel,
                        'submitted_at' => $submittedAt,
                        'status_type' => 'submitted',
                        'status_label' => '✓ Submitted',
                    ];
                } elseif ($this->status === 'in_progress' && $this->current_stage === 'co_supervisors') {
                    $timeline[] = [
                        'role' => $roleLabel,
                        'name' => $nameLabel,
                        'submitted_at' => null,
                        'status_type' => 'pending',
                        'status_label' => '⏳ Pending',
                    ];
                }
            }
        }

        // 4. DPGC
        if ($this->dpgc_submitted_at || $this->dpgc_recommendation !== null) {
            $timeline[] = [
                'role' => 'DPGC',
                'name' => $this->dpgcUser?->name ?? 'DPGC Convener',
                'submitted_at' => $this->dpgc_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'dpgc') {
            $timeline[] = [
                'role' => 'DPGC',
                'name' => $this->dpgcUser?->name ?? 'DPGC Convener',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 5. HOD
        if ($this->hod_submitted_at || $this->hod_recommendation !== null) {
            $timeline[] = [
                'role' => 'HOD',
                'name' => $this->hodUser?->name ?? 'Head of Department',
                'submitted_at' => $this->hod_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'hod') {
            $timeline[] = [
                'role' => 'HOD',
                'name' => $this->hodUser?->name ?? 'Head of Department',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 6. Academic Office
        if ($this->academic_office_submitted_at || $this->academic_office_is_verified !== null) {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => $this->academicOfficeUser?->name ?? 'Academic Office Staff',
                'submitted_at' => $this->academic_office_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Verified',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'academic_office') {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => $this->academicOfficeUser?->name ?? 'Academic Office Staff',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 7. DOAA
        if ($this->doaa_submitted_at || $this->doaa_is_verified !== null) {
            $timeline[] = [
                'role' => 'DOAA',
                'name' => $this->doaaUser?->name ?? 'Dean of Academic Affairs',
                'submitted_at' => $this->doaa_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Evaluated',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'doaa') {
            $timeline[] = [
                'role' => 'DOAA',
                'name' => $this->doaaUser?->name ?? 'Dean of Academic Affairs',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 8. Senate Chairperson
        if ($this->senate_chairperson_submitted_at || $this->senate_chairperson_approval !== null) {
            $timeline[] = [
                'role' => 'Senate Chairperson',
                'name' => $this->senateChairpersonUser?->name ?? 'Senate Chairperson',
                'submitted_at' => $this->senate_chairperson_submitted_at,
                'status_type' => 'submitted',
                'status_label' => $this->senate_chairperson_approval ? '✓ Approved' : 'Rejected',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'senate_chairperson') {
            $timeline[] = [
                'role' => 'Senate Chairperson',
                'name' => $this->senateChairpersonUser?->name ?? 'Senate Chairperson',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 9. Reverted step if reverted
        $revertedItem = Thesis::getRevertedTimelineItem($this);
        if ($revertedItem) {
            $timeline[] = $revertedItem;
        }

        return $timeline;
    }
}
