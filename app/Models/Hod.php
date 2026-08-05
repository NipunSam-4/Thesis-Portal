<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hod extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'name'];

    /**
     * Get the department this HoD belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all of the HoD's submission reviews/comments.
     */
    public function submissionReviews(): MorphMany
    {
        return $this->morphMany(SubmissionReview::class, 'reviewer');
    }
}