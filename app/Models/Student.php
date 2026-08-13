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
        'roll_number',
        'name',
        'date_confirmation',
    ];

    protected function casts(): array
    {
        return [
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

    /**
     * Determine the current thesis stage for the student.
     * Possible stages: Unregistered, PTS-1, PTS-2/PTS-3, PTS-4, PTS-5, PTS-6, Rejected
     */
    public function getThesisStageLabel(): string
    {
        $thesis = $this->theses->last();

        if (!$thesis) {
            return 'Unregistered';
        }

        $pts1 = $thesis->pts1Form;
        $pts2 = $thesis->pts2Form;

        // If any active form is rejected
        // if (($pts1 && $pts1->status === 'rejected') || ($pts2 && $pts2->status === 'rejected')) {
        //     return 'Rejected';
        // }

        // If PTS-1 is not yet approved
        if (!$pts1 || $pts1->status !== 'accepted') {
            return 'PTS-1';
        }

        // If PTS-1 is approved, student moves to PTS-2 / PTS-3
        if (!$pts2 || $pts2->status !== 'accepted') {
            return 'PTS-2/PTS-3';
        }

        return 'PTS-4';
    }

    /**
     * Check whether an active form for this student requires endorsement/evaluation by the given faculty user.
     * Optionally filtered by role: 'main', 'co', 'pspc', 'dpgc', 'hod', 'section_officer', 'doaa'.
     */
    public function requiresActionFromUser(User $user, ?string $roleFilter = null): bool
    {
        $thesis = $this->theses->last();
        if (!$thesis) {
            return false;
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
                for ($i = 1; $i <= 3; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                        return true;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'pspc') && $pts2->current_stage === 'pspc_members') {
                for ($i = 1; $i <= 3; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $recCol = "pspc_member_{$i}_recommendation";
                    if ($pts2->$idCol === $user->id && is_null($pts2->$recCol)) {
                        return true;
                    }
                }
            }
            if ((!$roleFilter || $roleFilter === 'dpgc') && $pts2->current_stage === 'dpgc' && $user->isDpgc()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'hod') && $pts2->current_stage === 'hod' && $user->isHod()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'section_officer') && $pts2->current_stage === 'section_officer' && $user->isSectionOfficer()) {
                return true;
            }
            if ((!$roleFilter || $roleFilter === 'doaa') && $pts2->current_stage === 'doaa' && $user->isDoaa()) {
                return true;
            }
        }

        return false;
    }
}