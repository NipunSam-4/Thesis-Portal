<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'program_name',
        'admission_category',
        'course_credits_earned',
        'course_credits_required',
        'roll_number',
        'date_joining',
        'date_registration',
        'date_confirmation',
        'phone_number',
        'phone_country_code',
        'phone_iso2',
        'alternate_phone_number',
        'alternate_phone_country_code',
        'alternate_phone_iso2',
        'alternate_email',
        'hindi_name',
        'current_address',
    ];

    protected function casts(): array
    {
        return [
            'course_credits_earned' => 'float',
            'course_credits_required' => 'float',
            'date_registration' => 'date:d-m-Y',
            'date_joining' => 'date:d-m-Y',
            'date_confirmation' => 'date:d-m-Y',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function isPhd(): bool
    {
        return $this->program_name === 'phd';
    }

    public function isMsr(): bool
    {
        return $this->program_name === 'msr';
    }

    public function getSearchableTextAttribute(): string
    {
        $parts = [
            $this->user->name ?? '',
            $this->roll_number ?? '',
            $this->user->email ?? '',
            $this->department->code ?? '',
            $this->department->name ?? '',
            $this->theses?->pluck('title')->join(' ') ?? '',
        ];

        return strtolower(implode(' ', array_filter($parts)));
    }


    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
    }

    public function activeThesis(): HasOne
    {
        return $this->hasOne(Thesis::class)->where('status', 'in_progress')->latestOfMany();
    }

    public function completedThesis(): HasOne
    {
        return $this->hasOne(Thesis::class)->where('status', 'completed')->latestOfMany();
    }

    public function rejectedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class)->where('status', 'rejected')->latest();
    }

    public function getCurrentThesisAttribute(): ?Thesis
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->whereIn('status', ['in_progress', 'completed'])->sortByDesc('id')->first();
        }
        return $this->theses()->whereIn('status', ['in_progress', 'completed'])->latest()->first();
    }

    public function getRejectedThesesAttribute()
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->where('status', 'rejected')->sortByDesc('id');
        }
        return $this->rejectedTheses()->get();
    }

    public function hasActiveThesis(): bool
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->where('status', 'in_progress')->isNotEmpty();
        }
        return $this->activeThesis()->exists();
    }

    public function hasCompletedThesis(): bool
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->where('status', 'completed')->isNotEmpty();
        }
        return $this->theses()->where('status', 'completed')->exists();
    }

    public function hasRejectedThesis(): bool
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->where('status', 'rejected')->isNotEmpty();
        }
        return $this->theses()->where('status', 'rejected')->exists();
    }

    public function canInitiateThesis(): bool
    {
        if ($this->relationLoaded('theses')) {
            return $this->theses->isEmpty();
        }
        return $this->theses()->doesntExist();
    }

    public function canReinitiateThesis(): bool
    {
        return !$this->hasActiveThesis() 
            && !$this->hasCompletedThesis() 
            && $this->hasRejectedThesis();
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_supervisor', 'student_id', 'faculty_user_id')
                    ->withPivot('supervisor_type')
                    ->withTimestamps();
    }

    public function mainSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_supervisor', 'student_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'main')
                    ->withTimestamps();
    }

    public function coSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_supervisor', 'student_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'co')
                    ->withTimestamps();
    }

    public function externalSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_external_supervisors', 'student_id', 'faculty_user_id')
                    ->with('externalSupervisorProfile')
                    ->withTimestamps();
    }

    public function pspcMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_pspc_members', 'student_id', 'faculty_user_id')
                    ->withTimestamps();
    }

    public function allCoSupervisors()
    {
        return $this->coSupervisors->merge($this->externalSupervisors);
    }

    /**
     * Get the currently active Main Supervisor for this student.
     */
    public function getActiveMainSupervisorAttribute(): ?User
    {
        return $this->mainSupervisors()->where('users.is_active', true)->first();
    }

    /**
     * Get all currently active internal Co-Supervisors.
     */
    public function activeCoSupervisors()
    {
        return $this->coSupervisors()->where('users.is_active', true)->get();
    }

    /**
     * Get all currently active External Supervisors.
     */
    public function activeExternalSupervisors()
    {
        return $this->externalSupervisors()->where('users.is_active', true)->get();
    }

    /**
     * Get all active Co-Supervisors (Internal + External combined).
     */
    public function activeAllCoSupervisors()
    {
        return $this->activeCoSupervisors()->merge($this->activeExternalSupervisors());
    }

    /**
     * Get all currently active PSPC Members.
     */
    public function activePspcMembers()
    {
        return $this->pspcMembers()->where('users.is_active', true)->get();
    }

    /**
     * Get the currently active Head of Department for this student's department.
     */
    public function getActiveHodAttribute(): ?User
    {
        return $this->department?->active_hod;
    }

    /**
     * Get the currently active DPGC Convener for this student's department.
     */
    public function getActiveDpgcAttribute(): ?User
    {
        return $this->department?->active_dpgc;
    }

    public function isMainSupervisor(User $user): bool
    {
        if ($this->relationLoaded('mainSupervisors')) {
            return $this->mainSupervisors->contains(fn($u) => (int)$u->id === (int)$user->id);
        }
        return $this->mainSupervisors()->where('users.id', $user->id)->exists();
    }

    public function isCoSupervisor(User $user): bool
    {
        return $this->isInternalCoSupervisor($user) || $this->isExternalSupervisor($user);
    }

    public function isSupervisor(User $user): bool
    {
        return $this->isMainSupervisor($user) || $this->isCoSupervisor($user);
    }

    public function isInternalCoSupervisor(User $user): bool
    {
        if ($this->relationLoaded('coSupervisors')) {
            return $this->coSupervisors->contains(fn($u) => (int)$u->id === (int)$user->id);
        }
        return $this->coSupervisors()->where('users.id', $user->id)->exists();
    }

    public function isExternalSupervisor(User $user): bool
    {
        if ($this->relationLoaded('externalSupervisors')) {
            return $this->externalSupervisors->contains(fn($u) => (int)$u->id === (int)$user->id);
        }
        return $this->externalSupervisors()->where('users.id', $user->id)->exists();
    }

    public function isPspcMember(User $user): bool
    {
        if ($this->relationLoaded('pspcMembers')) {
            return $this->pspcMembers->contains(fn($u) => (int)$u->id === (int)$user->id);
        }
        return $this->pspcMembers()->where('users.id', $user->id)->exists();
    }

    public function getExternalSupervisorIndex(User $user): ?int
    {
        $index = $this->externalSupervisors->search(fn($sup) => (int)$sup->id === (int)$user->id);
        return $index !== false ? $index + 1 : null;
    }

    // Get 1-based index among internal co-supervisors only (strictly excludes external supervisors)
    public function getCoSupervisorIndex(User $user): ?int
    {
        $index = $this->coSupervisors->search(fn($sup) => (int)$sup->id === (int)$user->id);
        return $index !== false ? $index + 1 : null;
    }

    public function getPspcMemberIndex(User $user): ?int
    {
        $index = $this->pspcMembers->search(fn($mem) => (int)$mem->id === (int)$user->id);
        return $index !== false ? $index + 1 : null;
    }

    public function getSupervisorRoleTitle(User $user): string
    {
        if ($this->isMainSupervisor($user)) {
            return 'Main Supervisor';
        }

        if ($this->isExternalSupervisor($user)) {
            $extIndex = $this->getExternalSupervisorIndex($user);
            return ($this->externalSupervisors->count() > 1 && $extIndex)
                ? "External Supervisor {$extIndex}"
                : 'External Supervisor';
        }

        if ($this->isInternalCoSupervisor($user)) {
            $coIndex = $this->getCoSupervisorIndex($user);
            return ($this->coSupervisors->count() > 1 && $coIndex)
                ? "Co-Supervisor {$coIndex}"
                : 'Co-Supervisor';
        }

        if ($this->isPspcMember($user)) {
            $pspcIndex = $this->getPspcMemberIndex($user);
            return $pspcIndex ? "PSPC Member {$pspcIndex}" : 'PSPC Member';
        }

        return 'Supervisor';
    }

    // Determine the current thesis stage for the student.
    // Possible stages: Unregistered, Rejected, Completed, PTS-1 & Draft Synopsis, PTS-1, PTS-2, PTS-2 Extension, PTS-3 & PTS-4, PTS-4 Extension, PTS-5, PTS-6
    public function getThesisStageLabel(): string
    {
        if ($this->hasCompletedThesis()) {
            return 'Completed';
        }

        if ($this->canReinitiateThesis()) {
            return 'Rejected';
        }

        $thesis = $this->relationLoaded('theses') 
            ? $this->theses->where('status', 'in_progress')->sortByDesc('id')->first() 
            : $this->activeThesis;
        if (!$thesis) {
            return 'Unregistered';
        }

        $prefix = $this->isPhd() ? 'PTS' : 'MSRTS';

        $draft = $thesis->draftSynopsisCirculation;
        $pts1 = $thesis->pts1Form;
        $pts2 = $thesis->pts2Form;
        $pts2Ext = $thesis->pts2Extension;
        $pts3 = $thesis->pts3Form;
        $pts4 = $thesis->pts4Form;
        $pts4Ext = $thesis->pts4Extension;
        $pts5 = $thesis->pts5Form;
        $pts6 = $thesis->pts6Form;

        // Stage 1 (Parallel): PTS-1 & Draft Synopsis
        $pts1Approved = $pts1 && $pts1->status === 'approved';
        if (!$pts1Approved) {
            if ($draft && $draft->status === 'circulated') {
                return "{$prefix}-1 & Draft Synopsis";
            }
            return "{$prefix}-1";
        }

        $pts2Approved = $pts2 && $pts2->status === 'approved';
        $pts3Approved = $pts3 && $pts3->status === 'approved';
        $pts4Accepted = $pts4 && $pts4->status === 'accepted';

        // Parallel Tracks after PTS-1 Approved:
        // - Track A: PTS-2 (and PTS-2 Extension) -> once approved -> PTS-4 (and PTS-4 Extension)
        // - Track B: PTS-3 (Panels of Examiners)
        $activeStages = [];

        // Track A1: PTS-2 / PTS-2 Extension (runs until PTS-2 is approved)
        if (!$pts2Approved) {
            if ($pts2Ext && $pts2Ext->status === 'in_progress') {
                if ($pts2 && $pts2->status === 'in_progress') {
                    $activeStages[] = "{$prefix}-2 & {$prefix}-2 Extension";
                } else {
                    $activeStages[] = "{$prefix}-2 Extension";
                }
            } else {
                $activeStages[] = "{$prefix}-2";
            }
        }

        // Track B: PTS-3 (opens post PTS-1 approved, runs until PTS-3 approved)
        if (!$pts3Approved) {
            $activeStages[] = "{$prefix}-3";
        }

        // Track A2: PTS-4 / PTS-4 Extension (opens only after PTS-2 approved, runs until PTS-4 accepted)
        if ($pts2Approved && !$pts4Accepted) {
            if ($pts4Ext && $pts4Ext->status === 'in_progress') {
                $activeStages[] = "{$prefix}-4 Extension";
            } else {
                $activeStages[] = "{$prefix}-4";
            }
        }

        if (!empty($activeStages)) {
            return implode(' & ', $activeStages);
        }

        // Stage 5 (Sequential): PTS-5 (Examiner Reports / Defense - Requires both PTS-3 Approved & PTS-4 Accepted)
        $pts5Approved = $pts5 && $pts5->status === 'approved';
        if (!$pts5Approved) {
            return "{$prefix}-5";
        }

        // Stage 6 (Sequential): PTS-6 (Final Approval & Degree Award)
        $pts6Approved = $pts6 && $pts6->status === 'approved';
        if (!$pts6Approved) {
            return "{$prefix}-6";
        }

        return 'Completed';
    }

    // Get all pending form action items for a given viewing authority user.
    // Handles parallel stages:
    // - Stage 1: PTS-1 & Draft Synopsis (parallel)
    // - Stage 2 & 3: PTS-2 / PTS-2 Extension & PTS-3 (parallel, post PTS-1 approved)
    // - Stage 4: PTS-4 & PTS-4 Extension (post PTS-2 approved, parallel with ongoing PTS-3)
    // - Stage 5 & 6: PTS-5 & PTS-6 (sequential)
    public function getPendingActionItemsForUser(User $user, ?string $roleFilter = null): array
    {
        $thesis = $this->relationLoaded('theses') 
            ? $this->theses->where('status', 'in_progress')->sortByDesc('id')->first() 
            : $this->activeThesis;
        if (!$thesis) {
            return [];
        }

        $items = [];
        $prefix = $this->isPhd() ? 'PTS' : 'MSRTS';

        $draft = $thesis->draftSynopsisCirculation;
        $pts1 = $thesis->pts1Form;
        $pts1Approved = $pts1 && $pts1->status === 'approved';

        $pts2 = $thesis->pts2Form;
        $pts2Ext = $thesis->pts2Extension;
        $pts2Approved = $pts2 && $pts2->status === 'approved';

        $pts3 = $thesis->pts3Form;
        $pts3Approved = $pts3 && $pts3->status === 'approved';

        $pts4 = $thesis->pts4Form;
        $pts4Ext = $thesis->pts4Extension;
        $pts4Accepted = $thesis->pts4Accepted ?? ($pts4 && $pts4->status === 'accepted');

        $pts5 = $thesis->pts5Form;
        $pts5Approved = $pts5 && $pts5->status === 'approved';

        $pts6 = $thesis->pts6Form;

        // ==========================================
        // STAGE 1 (PARALLEL): PTS-1 & Draft Synopsis
        // ==========================================

        // 1.1 Draft Synopsis Circulation (active while circulated and before PTS-1 is approved)
        if ($draft && $draft->status === 'circulated' && !$pts1Approved) {
            $hasCommented = $draft->comments->where('user_id', $user->id)->isNotEmpty();
            if (!$hasCommented) {
                if ((!$roleFilter || $roleFilter === 'main') && $this->isMainSupervisor($user)) {
                    $items[] = 'Draft Synopsis';
                } elseif ((!$roleFilter || $roleFilter === 'co') && $this->isCoSupervisor($user)) {
                    $items[] = 'Draft Synopsis';
                } elseif ((!$roleFilter || $roleFilter === 'pspc') && $this->isPspcMember($user)) {
                    $items[] = 'Draft Synopsis';
                }
            }
        }

        // 1.2 PTS-1 Form Action Check
        if ($pts1 && $pts1->status === 'in_progress') {
            $pts1NeedsAction = false;
            if ((!$roleFilter || $roleFilter === 'main') && $pts1->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                $pts1NeedsAction = true;
            }
            if ((!$roleFilter || $roleFilter === 'co') && $pts1->current_stage === 'co_supervisors') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts1->$idCol && (int)$pts1->$idCol === (int)$user->id && is_null($pts1->$recCol)) {
                        $pts1NeedsAction = true;
                        break;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'pspc') && $pts1->current_stage === 'pspc_members') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $recCol = "pspc_member_{$i}_recommendation";
                    if ($pts1->$idCol && (int)$pts1->$idCol === (int)$user->id && is_null($pts1->$recCol)) {
                        $pts1NeedsAction = true;
                        break;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'dpgc') && $pts1->current_stage === 'dpgc' && $user->isDpgc() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                $pts1NeedsAction = true;
            }
            if ((!$roleFilter || $roleFilter === 'hod') && $pts1->current_stage === 'hod' && $user->isHod() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                $pts1NeedsAction = true;
            }
            if ((!$roleFilter || $roleFilter === 'academic_office') && $pts1->current_stage === 'academic_office' && ($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority()))) {
                $pts1NeedsAction = true;
            }
            if ((!$roleFilter || $roleFilter === 'doaa') && $pts1->current_stage === 'doaa' && ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts1->acting_doaa_email === $user->email || $pts1->vested_doaa_email === $user->email)))) {
                $pts1NeedsAction = true;
            }

            if ($pts1NeedsAction) {
                $items[] = "{$prefix}-1";
            }
        }

        // ==========================================
        // STAGE 2 & 3 (PARALLEL): PTS-2 / PTS-2 Extension & PTS-3 (Requires PTS-1 Approved)
        // ==========================================
        if ($pts1Approved) {
            // 2.1 PTS-2 Form Action Check
            if ($pts2 && $pts2->status === 'in_progress') {
                $pts2NeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts2->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts2NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'co') && $pts2->current_stage === 'co_supervisors') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts2->$idCol && (int)$pts2->$idCol === (int)$user->id && is_null($pts2->$recCol)) {
                            $pts2NeedsAction = true;
                            break;
                        }
                    }
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts2->current_stage === 'academic_office' && ($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority()))) {
                    $pts2NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts2->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)))) {
                    $pts2NeedsAction = true;
                }

                if ($pts2NeedsAction) {
                    $items[] = "{$prefix}-2";
                }
            }

            // 2.2 PTS-2 Extension Action Check (parallel with PTS-2)
            if ($pts2Ext && $pts2Ext->status === 'in_progress' && !$pts2Approved) {
                $pts2ExtNeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts2Ext->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts2ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'dpgc') && $pts2Ext->current_stage === 'dpgc' && $user->isDpgc() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts2ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'hod') && $pts2Ext->current_stage === 'hod' && $user->isHod() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts2ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts2Ext->current_stage === 'academic_office' && $user->isAcademicOffice()) {
                    $pts2ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts2Ext->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2Ext->acting_doaa_email === $user->email || $pts2Ext->vested_doaa_email === $user->email)))) {
                    $pts2ExtNeedsAction = true;
                }

                if ($pts2ExtNeedsAction) {
                    $items[] = "{$prefix}-2 Extension";
                }
            }

            // 3.1 PTS-3 Action Check (parallel with PTS-2, opens post PTS-1 approved)
            if ($pts3 && $pts3->status === 'in_progress') {
                $pts3NeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts3->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts3NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'co') && $pts3->current_stage === 'co_supervisors') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts3->$idCol && (int)$pts3->$idCol === (int)$user->id && is_null($pts3->$recCol)) {
                            $pts3NeedsAction = true;
                            break;
                        }
                    }
                }
                if ((!$roleFilter || $roleFilter === 'dpgc') && $pts3->current_stage === 'dpgc' && $user->isDpgc() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts3NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'hod') && $pts3->current_stage === 'hod' && $user->isHod() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts3NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts3->current_stage === 'academic_office' && ($user->isAcademicOffice() || ($user->isGlobalAuthority() && !$user->isActingApprovalAuthority()))) {
                    $pts3NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts3->current_stage === 'doaa' && ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts3->acting_doaa_email === $user->email || $pts3->vested_doaa_email === $user->email)))) {
                    $pts3NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'senate_chairperson') && $pts3->current_stage === 'senate_chairperson' && $user->isSenateChairperson()) {
                    $pts3NeedsAction = true;
                }

                if ($pts3NeedsAction) {
                    $items[] = "{$prefix}-3";
                }
            } elseif (($pts3 && in_array($pts3->status, ['reverted', 'rejected'])) && ((!$roleFilter || $roleFilter === 'main') && $this->isMainSupervisor($user))) {
                // Main supervisor action to initiate or resubmit PTS-3
                $items[] = "{$prefix}-3";
            } elseif (!$pts3 && ((!$roleFilter || $roleFilter === 'main') && $this->isMainSupervisor($user))) {
                // Main supervisor action to initiate PTS-3 after PTS-1 is approved
                $items[] = "{$prefix}-3";
            }
        }

        // ==========================================
        // STAGE 4: PTS-4 & PTS-4 Extension (Requires PTS-2 Approved)
        // ==========================================
        if ($pts2Approved) {
            // 4.1 PTS-4 Action Check
            if ($pts4 && $pts4->status === 'in_progress') {
                $pts4NeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts4->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts4NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'co') && $pts4->current_stage === 'co_supervisors') {
                    for ($i = 1; $i <= 10; $i++) {
                        $idCol = "co_supervisor_{$i}_id";
                        $recCol = "co_supervisor_{$i}_recommendation";
                        if ($pts4->$idCol && (int)$pts4->$idCol === (int)$user->id && is_null($pts4->$recCol)) {
                            $pts4NeedsAction = true;
                            break;
                        }
                    }
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts4->current_stage === 'academic_office' && $user->isAcademicOffice()) {
                    $pts4NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'dr') && $pts4->current_stage === 'dr' && ($user->isDr())) {
                    $pts4NeedsAction = true;
                }

                if ($pts4NeedsAction) {
                    $items[] = "{$prefix}-4";
                }
            }

            // 4.2 PTS-4 Extension Action Check (parallel with PTS-4)
            if ($pts4Ext && $pts4Ext->status === 'in_progress' && !$pts4Accepted) {
                $pts4ExtNeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts4Ext->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts4ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'dpgc') && $pts4Ext->current_stage === 'dpgc' && $user->isDpgc() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts4ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'hod') && $pts4Ext->current_stage === 'hod' && $user->isHod() && $user->deptAuthorityProfile?->department_id === $this->department_id) {
                    $pts4ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts4Ext->current_stage === 'academic_office' && $user->isAcademicOffice()) {
                    $pts4ExtNeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts4Ext->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts4Ext->acting_doaa_email === $user->email || $pts4Ext->vested_doaa_email === $user->email)))) {
                    $pts4ExtNeedsAction = true;
                }

                if ($pts4ExtNeedsAction) {
                    $items[] = "{$prefix}-4 Extension";
                }
            }
        }

        // ==========================================
        // STAGE 5 (SEQUENTIAL): PTS-5 (Evaluation / Defense - Post PTS-4 & PTS-3 Approved)
        // ==========================================
        if ($pts4Accepted && $pts3Approved) {
            if ($pts5 && $pts5->status === 'in_progress') {
                $pts5NeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'main') && $pts5->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                    $pts5NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts5->current_stage === 'academic_office' && $user->isAcademicOffice()) {
                    $pts5NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts5->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts5->acting_doaa_email === $user->email || $pts5->vested_doaa_email === $user->email)))) {
                    $pts5NeedsAction = true;
                }

                if ($pts5NeedsAction) {
                    $items[] = "{$prefix}-5";
                }
            }
        }

        // ==========================================
        // STAGE 6 (SEQUENTIAL): PTS-6 (Final Approval - Post PTS-5 Approved)
        // ==========================================
        if ($pts5Approved) {
            if ($pts6 && $pts6->status === 'in_progress') {
                $pts6NeedsAction = false;
                if ((!$roleFilter || $roleFilter === 'academic_office') && $pts6->current_stage === 'academic_office' && $user->isAcademicOffice()) {
                    $pts6NeedsAction = true;
                }
                if ((!$roleFilter || $roleFilter === 'doaa') && $pts6->current_stage === 'doaa' && ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts6->acting_doaa_email === $user->email || $pts6->vested_doaa_email === $user->email)))) {
                    $pts6NeedsAction = true;
                }

                if ($pts6NeedsAction) {
                    $items[] = "{$prefix}-6";
                }
            }
        }

        return array_values(array_unique($items));
    }

    // Get the formatted Action Required badge label for display in dashboard cards.
    public function getActionRequiredBadgeLabel(User $user, ?string $roleFilter = null): ?string
    {
        $items = $this->getPendingActionItemsForUser($user, $roleFilter);
        if (empty($items)) {
            return null;
        }

        if (count($items) === 1) {
            return 'Action Required: ' . $items[0];
        }

        if (count($items) === 2) {
            return 'Action Required: ' . $items[0] . ' & ' . $items[1];
        }

        $last = array_pop($items);
        return 'Action Required: ' . implode(', ', $items) . ' & ' . $last;
    }

    // Check whether an active form for this student requires endorsement/evaluation by the given user.
    public function requiresActionFromUser(User $user, ?string $roleFilter = null): bool
    {
        return !empty($this->getPendingActionItemsForUser($user, $roleFilter));
    }

    // Get sorting priority score for a given viewing authority user.
    // Lower numeric score = higher priority in the list.
    // Tier 1 (10-50): Forms requiring THIS user's action
    // Tier 2 (100-160): Forms in progress anywhere in pipeline (lowest PTS form first)
    // Tier 3 (200-400): Completed/Terminated forms (Reverted > Rejected > Approved)
    // Tier 4 (500): Pending / Not started forms
    public function getAuthoritySortScore(User $user): int
    {
        $thesis = $this->relationLoaded('theses') 
            ? $this->theses->where('status', 'in_progress')->sortByDesc('id')->first() 
            : $this->activeThesis;
        if (!$thesis) {
            return 500; // Tier 4: No active thesis registered
        }

        $items = $this->getPendingActionItemsForUser($user);
        if (!empty($items)) {
            // Tier 1: Action required from this user (lowest active stage gets highest priority)
            if (in_array('PTS-1', $items) || in_array('MSRTS-1', $items) || in_array('Draft Synopsis', $items)) {
                return 10;
            }
            if (in_array('PTS-2', $items) || in_array('MSRTS-2', $items) || in_array('PTS-2 Extension', $items) || in_array('MSRTS-2 Extension', $items)) {
                return 20;
            }
            if (in_array('PTS-3', $items) || in_array('MSRTS-3', $items) || in_array('PTS-4', $items) || in_array('MSRTS-4', $items) || in_array('PTS-4 Extension', $items) || in_array('MSRTS-4 Extension', $items)) {
                return 30;
            }
            if (in_array('PTS-5', $items) || in_array('MSRTS-5', $items)) {
                return 40;
            }
            if (in_array('PTS-6', $items) || in_array('MSRTS-6', $items)) {
                return 50;
            }
            return 10;
        }

        $pts1 = $thesis->pts1Form;
        $pts2Ext = $thesis->pts2Extension;
        $pts2 = $thesis->pts2Form;
        $draft = $thesis->draftSynopsisCirculation;
        $pts3 = $thesis->pts3Form;
        $pts4 = $thesis->pts4Form;
        $pts4Ext = $thesis->pts4Extension;
        $pts5 = $thesis->pts5Form;
        $pts6 = $thesis->pts6Form;

        // --- TIER 2: In-Progress in Pipeline (Lowest PTS form first) ---
        if (($pts1 && $pts1->status === 'in_progress') || ($draft && $draft->status === 'circulated')) return 100;
        if ($pts2Ext && $pts2Ext->status === 'in_progress') return 110;
        if ($pts2 && $pts2->status === 'in_progress') return 120;
        if ($pts3 && $pts3->status === 'in_progress') return 130;
        if ($pts4Ext && $pts4Ext->status === 'in_progress') return 135;
        if ($pts4 && $pts4->status === 'in_progress') return 140;
        if ($pts5 && $pts5->status === 'in_progress') return 150;
        if ($pts6 && $pts6->status === 'in_progress') return 160;

        // --- TIER 3: Reverted > Rejected > Approved ---
        $allForms = array_filter([$pts1, $pts2Ext, $pts2, $draft, $pts3, $pts4Ext, $pts4, $pts5, $pts6]);
        if (!empty($allForms)) {
            // Check for reverted
            foreach ($allForms as $f) {
                if (isset($f->status) && $f->status === 'reverted') {
                    return 200;
                }
            }
            // Check for rejected
            foreach ($allForms as $f) {
                if (isset($f->status) && in_array($f->status, ['rejected', 'not_accepted'])) {
                    return 300;
                }
            }
            // Check for approved / completed / accepted
            foreach ($allForms as $f) {
                if (isset($f->status) && in_array($f->status, ['approved', 'accepted', 'completed'])) {
                    return 400;
                }
            }
        }

        // --- TIER 4: Pending / Not started ---
        return 500;
    }
}