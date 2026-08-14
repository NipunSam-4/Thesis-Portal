<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftSynopsisComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_synopsis_circulation_id',
        'user_id',
        'authority_role',
        'authority_label',
        'comment',
    ];

    public function circulation(): BelongsTo
    {
        return $this->belongsTo(DraftSynopsisCirculation::class, 'draft_synopsis_circulation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
