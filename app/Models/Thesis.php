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
        return $this->hasOne(Pts1Form::class)->latestOfMany();
    }

    public function pts1Forms(): HasMany
    {
        return $this->hasMany(Pts1Form::class);
    }

    public function pts2Form(): HasOne
    {
        return $this->hasOne(Pts2Form::class);
    }

    public function pts2Forms(): HasMany
    {
        return $this->hasMany(Pts2Form::class);
    }

    /**
     * Accessor to get human-readable formatted status (e.g. 'in_progress' -> 'In Progress').
     */
    public function getCurrentStatusAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status ?? ''));
    }

    /**
     * Accessor to dynamically determine the currently active form ('pts1', 'pts2', etc.).
     */
    public function getActiveFormAttribute(): string
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'accepted') {
            return 'pts1';
        }
        if (!$this->pts2Form || $this->pts2Form->status !== 'accepted') {
            return 'pts2';
        }
        return 'completed';
    }

    /**
     * Accessor to get the human-readable stage of the active form.
     */
    public function getActiveStageLabelAttribute(): string
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'accepted') {
            return $this->pts1Form ? ucwords(str_replace('_', ' ', $this->pts1Form->current_stage)) : 'PTS-1 Not Submitted';
        }
        if (!$this->pts2Form || $this->pts2Form->status !== 'accepted') {
            return $this->pts2Form ? ucwords(str_replace('_', ' ', $this->pts2Form->current_stage)) : 'PTS-2 Not Submitted';
        }
        return 'Thesis Workflow Completed';
    }
}