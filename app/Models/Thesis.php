<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Thesis extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'current_status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisor', 'thesis_id', 'faculty_user_id')
                    ->withPivot('supervisor_type')
                    ->withTimestamps();
    }

    public function mainSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisor', 'thesis_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'main')
                    ->withTimestamps();
    }

    public function coSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisor', 'thesis_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'co')
                    ->withTimestamps();
    }

    public function administrativeSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisor', 'thesis_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'administrative')
                    ->withTimestamps();
    }

    public function externalSupervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_supervisor', 'thesis_id', 'faculty_user_id')
                    ->wherePivot('supervisor_type', 'external')
                    ->withTimestamps();
    }

    public function pspcMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thesis_pspc_members', 'thesis_id', 'faculty_user_id')
                    ->withTimestamps();
    }

    public function pts1Form(): HasOne
    {
        return $this->hasOne(Pts1Form::class);
    }

    public function pts2Form(): HasOne
    {
        return $this->hasOne(Pts2Form::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(Examiner::class);
    }
}