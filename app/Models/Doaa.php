<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Doaa extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all of the DoAA's submission reviews/comments.
     */
    public function submissionReviews(): MorphMany
    {
        return $this->morphMany(SubmissionReview::class, 'reviewer');
    }
}