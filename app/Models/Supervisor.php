<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'employee_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

/**
     * Get ALL theses this faculty member is supervising.
     */
    public function theses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'thesis_supervisor')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Helper: Get ONLY the theses where they are the Main Supervisor.
     */
    public function mainTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'thesis_supervisor')
                    ->wherePivot('role', 'main')
                    ->withTimestamps();
    }
   
    /**
     * Helper: Get ONLY the theses where they act as a Co-Supervisor.
     */
    public function coTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'thesis_supervisor')
                    ->wherePivot('role', 'co')
                    ->withTimestamps();
    }

    /**
     * Helper: Get ONLY the theses where they act as an Administrative Supervisor.
     */
    public function administrativeTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'thesis_supervisor')
                    ->wherePivot('role', 'administrative')
                    ->withTimestamps();
    }

    /**
     * Helper: Get ONLY the theses where they act as an External Supervisor.
     */
    public function externalTheses(): BelongsToMany
    {
        return $this->belongsToMany(Thesis::class, 'thesis_supervisor')
                    ->wherePivot('role', 'external')
                    ->withTimestamps();
    }
}