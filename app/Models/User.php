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
    // @use HasFactory<UserFactory>
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

    // |--------------------------------------------------------------------------
    // | Role Helper Methods
    // |--------------------------------------------------------------------------

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

    public function isAdoaa(): bool
    {
        return $this->role === 'adoaa';
    }

    public function isDoaa(): bool
    {
        return $this->role === 'doaa';
    }

    public function isActingApprovalAuthority(): bool
    {
        return $this->role === 'acting_approval_authority';
    }

    public function isDeptAuthority(): bool
    {
        return in_array($this->role, ['dpgc', 'hod']);
    }

    public function isGlobalAuthority(): bool
    {
        return in_array($this->role, ['academic_office', 'adoaa', 'doaa', 'senate_chairperson', 'ar', 'dr']);
    }

    public function isSenateChairperson(): bool
    {
        return $this->role === 'senate_chairperson';
    }

    public function isArAcademic(): bool
    {
        return $this->role === 'ar';
    }

    public function isAr(): bool
    {
        return $this->isArAcademic();
    }
    
    public function isDrAcademic(): bool
    {
        return $this->role === 'dr';
    }

    public function isDr(): bool
    {
        return $this->isDrAcademic();
    }

    public function isAcademicOffice(): bool
    {
        return $this->role === 'academic_office';
    }

    public function isExternalExaminer(): bool
    {
        return $this->role === 'external_examiner';
    }

    public function isExternalSupervisor(): bool
    {
        return $this->role === 'external_supervisor';
    }

    /**
     * Get the currently active Academic Office user.
     */
    public static function getActiveAcademicOffice(): ?self
    {
        return self::where('role', 'academic_office')->where('is_active', true)->first();
    }

    /**
     * Get the currently active DOAA user.
     */
    public static function getActiveDoaa(): ?self
    {
        return self::where('role', 'doaa')->where('is_active', true)->first();
    }

    /**
     * Get the currently active Deputy Registrar (DR) user.
     */
    public static function getActiveDr(): ?self
    {
        return self::where('role', 'dr')->where('is_active', true)->first();
    }

    /**
     * Get the currently active Senate Chairperson user.
     */
    public static function getActiveSenateChairperson(): ?self
    {
        return self::where('role', 'senate_chairperson')->where('is_active', true)->first();
    }

    // |--------------------------------------------------------------------------
    // | Profile Relationships
    // |--------------------------------------------------------------------------

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function facultyProfile(): HasOne
    {
        return $this->hasOne(FacultyProfile::class);
    }

    public function deptAuthorityProfile(): HasOne
    {
        return $this->hasOne(DeptAuthorityProfile::class);
    }

    public function actingDoaa(): HasOne
    {
        return $this->hasOne(ActingDoaa::class);
    }

    public function vestedDoaa(): HasOne
    {
        return $this->hasOne(VestedDoaa::class);
    }

    public function externalSupervisorProfile(): HasOne
    {
        return $this->hasOne(ExternalSupervisorProfile::class);
    }

    public function externalSupervisedStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_external_supervisors', 'faculty_user_id', 'student_id')
                    ->withTimestamps();
    }
}