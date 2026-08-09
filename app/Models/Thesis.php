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
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function pts1Form(): HasOne
    {
        return $this->hasOne(Pts1Form::class);
    }

    public function pts2Form(): HasOne
    {
        return $this->hasOne(Pts2Form::class);
    }

    /**
     * Accessor to get human-readable formatted status (e.g. 'in_progress' -> 'In Progress').
     */
    public function getCurrentStatusAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status ?? ''));
    }
}