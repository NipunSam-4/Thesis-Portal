<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    // @var array<int, string>
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    // Get the attributes that should be cast.
    // @return array<string, string>
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Get the Department Super Admins assigned to this department.
    public function superAdmins(): HasMany
    {
        return $this->hasMany(SuperAdmin::class);
    }

    public function facultyProfiles(): HasMany
    {
        return $this->hasMany(FacultyProfile::class);
    }

    public function deptAuthorityProfiles(): HasMany
    {
        return $this->hasMany(DeptAuthorityProfile::class);
    }

    public function isHod(User $user): bool
    {
        return $user->isHod() && $this->deptAuthorityProfiles()->where('user_id', $user->id)->exists();
    }

    public function isDpgcConvener(User $user): bool
    {
        return $user->isDpgc() && $this->deptAuthorityProfiles()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the currently active Head of Department (HOD) for this department.
     */
    public function getActiveHodAttribute(): ?User
    {
        return User::where('role', 'hod')
            ->where('is_active', true)
            ->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $this->id))
            ->first();
    }

    /**
     * Get the currently active DPGC Convener for this department.
     */
    public function getActiveDpgcAttribute(): ?User
    {
        return User::where('role', 'dpgc')
            ->where('is_active', true)
            ->whereHas('deptAuthorityProfile', fn($q) => $q->where('department_id', $this->id))
            ->first();
    }
}