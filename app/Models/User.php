<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
     

    public function isFaculty(): bool
    {
        return $this->role === 'faculty';
    }

    public function isHod(): bool
    {
        return $this->role === 'hod';
    }

    public function isDpgc(): bool
    {
        return $this->role === 'dpgc';
    }

    public function isSectionOfficer(): bool
    {
        return $this->role === 'section_officer';
    }

    public function isAdoaa(): bool
    {
        return $this->role === 'adoaa';
    }

    public function isDoaa(): bool
    {
        return $this->role === 'doaa';
    }

    public function isSenateChairperson(): bool
    {
        return $this->role === 'senate_chairperson';
    }

    public function isArAcademic(): bool
    {
        return $this->role === 'ar';
    }
    
    public function isDrAcademic(): bool
    {
        return $this->role === 'dr';
    }

    public function isAcademicOffice(): bool
    {
        return $this->role === 'academic_office';
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Relationships
    |--------------------------------------------------------------------------
    */

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function facultyProfile(): HasOne
    {
        return $this->hasOne(FacultyProfile::class);
    }

    /**
     * Get the user's department from their student or faculty profile.
     */
    public function getDepartmentAttribute()
    {
        return $this->student?->department ?? $this->facultyProfile?->department;
    }

    /*
    |--------------------------------------------------------------------------
    | Faculty Thesis Relationships (Supervisors & PSPC)
    |--------------------------------------------------------------------------
    */

    public function supervisedTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'student_supervisor', 'faculty_user_id', 'student_id', 'id', 'student_id')
                    ->withPivot('supervisor_type')
                    ->withTimestamps();
    }

    public function pspcTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'student_pspc_members', 'faculty_user_id', 'student_id', 'id', 'student_id')
                    ->withTimestamps();
    }
}