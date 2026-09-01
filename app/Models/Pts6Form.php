<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts6Form extends Model
{
    use HasFactory;

    protected $table = 'pts6_forms';

    protected $fillable = [
        'thesis_id',
        'current_stage',
        'status',
        'acting_doaa_email',
        'vested_doaa_email',
        'approved_by_authority',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    // Accessor for human-readable stage label mapped from Thesis.
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }
}
