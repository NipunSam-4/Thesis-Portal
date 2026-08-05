<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SubmissionReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'reviewer_type',
        'action', // e.g., 'Recommend', 'Revert', 'Approve'
        'comment',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the owning reviewer model (Hod, Doaa, Supervisor, etc.)
     */
    public function reviewer(): MorphTo
    {
        return $this->morphTo();
    }
}