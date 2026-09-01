<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts3Examiner extends Model
{
    use HasFactory;

    protected $table = 'pts3_examiners';

    protected $guarded = [];

    protected $casts = [
        'has_consent' => 'boolean',
        'doaa_priority' => 'integer',
        'senate_chairperson_priority' => 'integer',
    ];

    public function pts3Form(): BelongsTo
    {
        return $this->belongsTo(Pts3Form::class);
    }
}
