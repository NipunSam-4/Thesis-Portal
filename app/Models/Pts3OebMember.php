<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts3OebMember extends Model
{
    use HasFactory;

    protected $table = 'pts3_oeb_members';

    protected $guarded = [];

    protected $casts = [
        'doaa_priority' => 'integer',
        'senate_chairperson_priority' => 'integer',
    ];

    public function pts3Form(): BelongsTo
    {
        return $this->belongsTo(Pts3Form::class);
    }
}
