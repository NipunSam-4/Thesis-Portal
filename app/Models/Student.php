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
        'name',
        'date_confirmation',
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

    public function hasActiveThesis(): bool
    {
        return $this->activeThesis()->exists();
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

    public function pspcMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_pspc_members', 'student_id', 'faculty_user_id')
                    ->withTimestamps();
    }

    public function isMainSupervisor(User $user): bool
    {
        return $this->mainSupervisors()->where('users.id', $user->id)->exists();
    }

    public function isCoSupervisor(User $user): bool
    {
        return $this->coSupervisors()->where('users.id', $user->id)->exists();
    }

    public function isPspcMember(User $user): bool
    {
        return $this->pspcMembers()->where('users.id', $user->id)->exists();
    }

    // Determine the current thesis stage for the student.
    // Possible stages: Unregistered, PTS-1, PTS-2/PTS-3, PTS-4, PTS-5, PTS-6, Rejected
    public function getThesisStageLabel(): string
    {
        $thesis = $this->theses->last();

        if (!$thesis) {
            return 'Unregistered';
        }

        $pts1 = $thesis->pts1Form;
        $pts2 = $thesis->pts2Form;
        $pts3 = $thesis->pts3Form;
        $pts4 = $thesis->pts4Form;
        $pts5 = $thesis->pts5Form;
        $pts6 = $thesis->pts6Form;


        $prefix = $this->isPhd() ? 'PTS' : 'MSRTS';

        // Check sequentially from Form 1 through Form 6
        if (!$pts1 || $pts1->status !== 'approved') {
            return $prefix . '-1';
        }

        if (!$pts2 || $pts2->status !== 'approved') {
            return $prefix . '-2';
        }

        if (!$pts3 || $pts3->status !== 'approved') {
            return $prefix . '-3';
        }

        if (!$pts4 || $pts4->status !== 'approved') {
            return $prefix . '-4';
        }

        if (!$pts5 || $pts5->status !== 'approved') {
            return $prefix . '-5';
        }

        if (!$pts6 || $pts6->status !== 'approved') {
            return $prefix . '-6';
        }

        return 'Thesis Workflow Completed';
    }

    // Check whether an active form for this student requires endorsement/evaluation by the given faculty user.
    // Optionally filtered by role: 'main', 'co', 'pspc', 'dpgc', 'hod', 'section_officer', 'doaa'.
    public function requiresActionFromUser(User $user, ?string $roleFilter = null): bool
    {
        $thesis = $this->theses->last();
        if (!$thesis) {
            return false;
        }

        // Check Draft Synopsis Action
        $draftSynopsis = $thesis->draftSynopsisCirculation;
        $pts1Approved = $thesis->pts1Form && $thesis->pts1Form->status === 'approved';
        if ($draftSynopsis && $draftSynopsis->status === 'circulated' && !$pts1Approved) {
            $hasCommented = $draftSynopsis->comments->where('user_id', $user->id)->isNotEmpty();
            if (!$hasCommented) {
                if ((!$roleFilter || $roleFilter === 'main') && $this->isMainSupervisor($user)) {
                    return true;
                }
                if ((!$roleFilter || $roleFilter === 'co') && $this->isCoSupervisor($user)) {
                    return true;
                }
                if ((!$roleFilter || $roleFilter === 'pspc') && $this->isPspcMember($user)) {
                    return true;
                }
            }
        }

        // Check PTS-1 Action
        $pts1 = $thesis->pts1Form;
        if ($pts1 && $pts1->status === 'in_progress') {
            if ((!$roleFilter || $roleFilter === 'main') && $pts1->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'co') && $pts1->current_stage === 'co_supervisors') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) {
                        return true;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'pspc') && $pts1->current_stage === 'pspc_members') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $recCol = "pspc_member_{$i}_recommendation";
                    if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) {
                        return true;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'dpgc') && $pts1->current_stage === 'dpgc' && $user->isDpgc()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'hod') && $pts1->current_stage === 'hod' && $user->isHod()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'section_officer') && $pts1->current_stage === 'section_officer' && $user->isSectionOfficer()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'doaa') && $pts1->current_stage === 'doaa' && $user->isDoaa()) {
                return true;
            }
        }

        // Check PTS-2 Action
        $pts2 = $thesis->pts2Form;
        if ($pts2 && $pts2->status === 'in_progress') {
            if ((!$roleFilter || $roleFilter === 'main') && $pts2->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'co') && $pts2->current_stage === 'co_supervisors') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                        return true;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'academic_office') && $pts2->current_stage === 'academic_office' && ($user->isAcademicOffice() || $user->isGlobalAuthority())) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'doaa') && $pts2->current_stage === 'doaa' && $user->isDoaa()) {
                return true;
            }
        }

        // Check PTS-2 Extension Action
        $pts2Ext = $thesis->pts2Extension;
        $pts2Approved = $thesis->pts2Form && $thesis->pts2Form->status === 'approved';
        if ($pts2Ext && $pts2Ext->status === 'in_progress' && !$pts2Approved) {
            if ((!$roleFilter || $roleFilter === 'main') && $pts2Ext->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'dpgc') && $pts2Ext->current_stage === 'dpgc' && $user->isDpgc()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'hod') && $pts2Ext->current_stage === 'hod' && $user->isHod()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'section_officer') && $pts2Ext->current_stage === 'section_officer' && $user->isSectionOfficer()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'doaa') && $pts2Ext->current_stage === 'doaa' && $user->isDoaa()) {
                return true;
            }
        }

        return false;
    }

    // Get sorting priority score for a given viewing authority user.
    // Lower numeric score = higher priority in the list.
    // Tier 1 (10-40): Forms requiring THIS user's action (PTS-1 > PTS-2 Ext > PTS-2 > Draft)
    // Tier 2 (100-130): Forms in progress anywhere in pipeline (lowest PTS form first)
    // Tier 3 (200-400): Completed/Terminated forms (Reverted > Rejected > Approved)
    // Tier 4 (500): Pending / Not started forms
    public function getAuthoritySortScore($user): int
    {
        $thesis = $this->theses->first();
        if (!$thesis) {
            return 500; // Tier 4: No thesis registered
        }

        $pts1 = $thesis->pts1Form;
        $pts2Ext = $thesis->pts2Extension;
        $pts2 = $thesis->pts2Form;
        $draft = $thesis->draftSynopsisCirculation;
        $pts3 = $thesis->pts3Form;
        $pts4 = $thesis->pts4Form;
        $pts5 = $thesis->pts5Form;
        $pts6 = $thesis->pts6Form;

        // --- TIER 1: Requires THIS user's action / endorsement ---
        // 1.1 PTS-1 action required
        if ($pts1 && $pts1->status === 'in_progress') {
            if ($pts1->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) return 10;
            if ($pts1->current_stage === 'co_supervisors') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) return 10;
                }
            }
            if ($pts1->current_stage === 'pspc_members') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $recCol = "pspc_member_{$i}_recommendation";
                    if ($pts1->$idCol === $user->id && is_null($pts1->$recCol)) return 10;
                }
            }
            if ($pts1->current_stage === 'dpgc' && $user->isDpgc()) return 10;
            if ($pts1->current_stage === 'hod' && $user->isHod()) return 10;
            if ($pts1->current_stage === 'section_officer' && $user->isSectionOfficer()) return 10;
            if ($pts1->current_stage === 'doaa' && ($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic())) return 10;
        }

        // 1.2 PTS-2 Extension action required
        if ($pts2Ext && $pts2Ext->status === 'in_progress') {
            if ($pts2Ext->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) return 20;
            if ($pts2Ext->current_stage === 'dpgc' && $user->isDpgc()) return 20;
            if ($pts2Ext->current_stage === 'hod' && $user->isHod()) return 20;
            if ($pts2Ext->current_stage === 'section_officer' && $user->isSectionOfficer()) return 20;
            if ($pts2Ext->current_stage === 'doaa' && $user->isDoaa()) return 20;
        }

        // 1.3 PTS-2 action required
        if ($pts2 && $pts2->status === 'in_progress') {
            if ($pts2->current_stage === 'main_supervisor' && $this->isMainSupervisor($user)) return 30;
            if ($pts2->current_stage === 'co_supervisors') {
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) return 30;
                }
            }
            if ($pts2->current_stage === 'academic_office' && ($user->isAcademicOffice() || $user->isGlobalAuthority())) return 30;
            if ($pts2->current_stage === 'doaa' && $user->isDoaa()) return 30;
        }

        // 1.4 Draft Synopsis action required
        if ($draft && $draft->status === 'in_progress') {
            $hasCommented = $draft->comments->where('user_id', $user->id)->isNotEmpty();
            if (!$hasCommented) {
                if ($this->isMainSupervisor($user) || $this->isCoSupervisor($user) || $this->isPspcMember($user)) {
                    return 40;
                }
            }
        }

        // --- TIER 2: In-Progress in Pipeline (Lowest PTS form first) ---
        if ($pts1 && $pts1->status === 'in_progress') return 100;
        if ($pts2Ext && $pts2Ext->status === 'in_progress') return 110;
        if ($pts2 && $pts2->status === 'in_progress') return 120;
        if ($draft && $draft->status === 'in_progress') return 130;

        // --- TIER 3: Reverted > Rejected > Approved ---
        $allForms = array_filter([$pts1, $pts2Ext, $pts2, $draft]);
        if (!empty($allForms)) {
            // Check for reverted
            foreach ($allForms as $f) {
                if (isset($f->status) && $f->status === 'reverted') {
                    return 200;
                }
            }
            // Check for rejected
            foreach ($allForms as $f) {
                if (isset($f->status) && $f->status === 'rejected') {
                    return 300;
                }
            }
            // Check for approved / completed
            foreach ($allForms as $f) {
                if (isset($f->status) && in_array($f->status, ['approved', 'completed'])) {
                    return 400;
                }
            }
        }

        // --- TIER 4: Pending / Not started ---
        return 500;
    }
}