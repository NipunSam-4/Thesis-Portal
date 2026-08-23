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
        'section_officer_recommendation',
        'section_officer_confidential_remark',
        'section_officer_submitted_at',
        'doaa_recommendation',
        'doaa_confidential_remark',
        'doaa_student_comment',
        'doaa_submitted_at',
        'approved_extended_until_date',
        'acting_doaa_email',
        'vested_doaa_email',
        'approved_by_authority',
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
            'section_officer_recommendation' => 'boolean',
            'section_officer_submitted_at' => 'datetime',
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
            'section_officer' => 4,
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
        if ($student && $student->isMainSupervisor($user)) {
            $userRanks[] = self::getRoleRank('main_supervisor');
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
        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            $userRanks[] = self::getRoleRank('doaa');
        }

        if (empty($userRanks)) {
            return false;
        }

        $minUserRank = min($userRanks);
        return $minUserRank <= $revertingRank;
    }

    // Format reverted by role label.
    public function getRevertedByRoleLabel(): string
    {
        return \App\Http\Controllers\ThesisController::getRevertedByRoleLabel($this);
    }
    
    // Get the reversion comment left by the reverting authority.
    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Accessor for human-readable stage label mapped from ThesisController.
    // Usage in Blade: {{ $pts2Extension->stage_label }}
    public function getStageLabelAttribute(): string
    {
        return \App\Http\Controllers\ThesisController::getStageLabel($this->current_stage);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        $timeline = [];
        $student = $this->thesis?->student;
        $deptId = $student?->department_id;

        $dpgcUser = $deptId ? User::where('role', 'dpgc')->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $deptId))->first() : null;
        $hodUser = $deptId ? User::where('role', 'hod')->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $deptId))->first() : null;
        $soUser = User::whereIn('role', ['section_officer', 'academic_office'])->first();
        $doaaUser = User::whereIn('role', ['doaa', 'adoaa'])->first();

        // 1. Student Application
        if ($this->created_at) {
            $timeline[] = [
                'role' => 'Student Application',
                'name' => $student?->user?->name ?? 'Student',
                'submitted_at' => $this->created_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        }

        // 2. Main Supervisor
        $mainSup = $student?->mainSupervisor ?? $student?->mainSupervisors?->first();
        if ($this->main_supervisor_submitted_at) {
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

        // 3. DPGC
        $dpgcSubmitted = $this->dpgc_submitted_at || $this->dpgc_recommendation !== null;
        if ($dpgcSubmitted) {
            $timeline[] = [
                'role' => 'DPGC Convenor',
                'name' => 'DPGC Convenor',
                'submitted_at' => $this->dpgc_submitted_at ?? $this->updated_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'dpgc') {
            $timeline[] = [
                'role' => 'DPGC Convenor',
                'name' => 'DPGC Convenor',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 4. HOD
        $hodSubmitted = $this->hod_submitted_at || $this->hod_recommendation !== null;
        if ($hodSubmitted) {
            $timeline[] = [
                'role' => 'Head of Department',
                'name' => 'HOD',
                'submitted_at' => $this->hod_submitted_at ?? $this->updated_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'hod') {
            $timeline[] = [
                'role' => 'Head of Department',
                'name' => 'HOD',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 5. Section Officer
        $soSubmitted = $this->section_officer_submitted_at || $this->section_officer_recommendation !== null;
        if ($soSubmitted) {
            $timeline[] = [
                'role' => 'Academic Office (SO)',
                'name' => 'Section Officer',
                'submitted_at' => $this->section_officer_submitted_at ?? $this->updated_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($this->status === 'in_progress' && $this->current_stage === 'section_officer') {
            $timeline[] = [
                'role' => 'Academic Office (SO)',
                'name' => 'Section Officer',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 6. DOAA
        $doaaSubmitted = $this->doaa_submitted_at || $this->doaa_recommendation !== null;
        if ($doaaSubmitted) {
            $isApproved = $this->status === 'approved' || $this->doaa_recommendation == true;
            $isRejected = $this->status === 'rejected' || $this->doaa_recommendation === false;
            $timeline[] = [
                'role' => 'Dean of Academic Affairs',
                'name' => 'DOAA',
                'submitted_at' => $this->doaa_submitted_at ?? $this->updated_at,
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

        // 7. Reverted Event (if reverted)
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
}
