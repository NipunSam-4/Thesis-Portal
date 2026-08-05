<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Department Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the Department Super Admins assigned to this department.
     */
    public function superAdmins(): HasMany
    {
        return $this->hasMany(SuperAdmin::class);
    }

    /**
     * Get the Head of Departments (HoDs) assigned to this department.
     */
    public function hods(): HasMany
    {
        return $this->hasMany(Hod::class);
    }

    /**
     * Get the PhD Students enrolled in this department.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
    
    /**
     * Get the Faculty Supervisors belonging to this department.
     */
    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class);
    }
}