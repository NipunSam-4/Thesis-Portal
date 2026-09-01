<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts4Extension extends Model
{
    use HasFactory;

    protected $table = 'pts4_extensions';

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
        'academic_office_recommendation',
        'academic_office_confidential_remark',
        'academic_office_submitted_at',
        'doaa_recommendation',
        'doaa_confidential_remark',
        'doaa_student_comment',
        'doaa_submitted_at',
        'approved_extended_until_date',
        'main_supervisor_id',
        'dpgc_user_id',
        'hod_user_id',
        'academic_office_user_id',
        'doaa_user_id',
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
            'academic_office_recommendation' => 'boolean',
            'academic_office_submitted_at' => 'datetime',
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

    // Get numerical rank for role in PTS-4 Extension workflow hierarchy.
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
            'academic_office' => 4,
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

    // Check if a given user is allowed to view the reverted PTS-4 Extension form.
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

    // Determine access status for in-progress submitted extension: 'allowed', 'pending_endorsement', 'not_reached', or 'unauthorized'
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

        $stage = $this->current_stage;
        $stageRank = self::getRoleRank($stage);
        $statuses = [];

        // 1. Main Supervisor (Rank 1)
        if ($student?->isMainSupervisor($user)) {
            if ($stageRank < 1) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 1) {
                $statuses[] = ($this->main_supervisor_submitted_at || $this->main_supervisor_recommendation !== null) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 2. DPGC (Rank 2)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 2) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 2) {
                $statuses[] = ($this->dpgc_submitted_at || !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 3. HOD (Rank 3)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 3) {
                $statuses[] = ($this->hod_submitted_at || !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 4. Academic Office (Rank 4)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 4) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 4) {
                $statuses[] = ($this->academic_office_submitted_at || !is_null($this->academic_office_recommendation)) ? 'allowed' : 'pending_endorsement';
            } else {
                $statuses[] = 'allowed';
            }
        }

        // 5. DOAA / Global Authorities (Rank 5)
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 5) {
                $statuses[] = 'not_reached';
            } elseif ($stageRank === 5) {
                $statuses[] = ($this->doaa_submitted_at || !is_null($this->doaa_recommendation)) ? 'allowed' : 'pending_endorsement';
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

    // Check if user is authorized to view this PTS-4 extension form in its current state
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

        if ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa()) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email)) {
            return true;
        }

        return false;
    }

    // Check if user is currently authorized to evaluate/endorse this PTS-4 extension form
    public function canUserEvaluate(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    // Format reverted by role label.
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
    // Usage in Blade: {{ $pts4Extension->stage_label }}
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    // Accessor for human-readable status label mapped from Thesis.
    // Usage in Blade: {{ $pts4Extension->status_label }}
    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    // Get array of completed submission timestamps for all authorities and student.
    public function getSubmittedTimeline(): array
    {
        $timeline = [];
        $student = $this->thesis?->student;
        $deptId = $student?->department_id;

        $dpgcUser = $deptId ? User::where('role', 'dpgc')->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $deptId))->first() : null;
        $hodUser = $deptId ? User::where('role', 'hod')->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $deptId))->first() : null;
        $soUser = User::where('role', 'academic_office')->first();
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

        // 5. Academic Office
        $soSubmitted = $this->academic_office_submitted_at || $this->academic_office_recommendation !== null;
        if ($soSubmitted) {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'submitted_at' => $this->academic_office_submitted_at ?? $this->updated_at,
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
        if ($revertedItem = Thesis::getRevertedTimelineItem($this)) {
            $timeline[] = $revertedItem;
        }

        return $timeline;
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
}
