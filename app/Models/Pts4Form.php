<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts4Form extends Model
{
    use HasFactory;

    protected $table = 'pts4_forms';

    protected $fillable = [
        'thesis_id',
        'current_stage',
        'status',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    // Accessor for human-readable stage label mapped from ThesisController.
    public function getStageLabelAttribute(): string
    {
        return \App\Http\Controllers\ThesisController::getStageLabel($this->current_stage);
    }
}
