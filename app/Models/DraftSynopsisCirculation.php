<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DraftSynopsisCirculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'student_id',
        'draft_synopsis_doc_path',
        'status',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DraftSynopsisComment::class);
    }

    /**
     * Get comments ordered by authority hierarchy.
     */
    public function getOrderedComments()
    {
        $comments = $this->comments()->with('user')->get();

        // Standard authority hierarchy weight mapping
        $roleWeights = [
            'main_supervisor' => 1,
            'co_supervisor' => 2,
            'pspc_member' => 3,
            'dpgc' => 4,
            'hod' => 5,
            'section_officer' => 6,
            'doaa' => 7,
        ];

        return $comments->sortBy(function ($comment) use ($roleWeights) {
            $baseWeight = $roleWeights[$comment->authority_role] ?? 99;
            return $baseWeight * 1000 + ($comment->id % 1000);
        })->values();
    }
}
