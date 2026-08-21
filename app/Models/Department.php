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
}