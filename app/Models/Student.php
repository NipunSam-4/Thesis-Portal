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
}