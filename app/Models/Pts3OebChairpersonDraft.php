<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts3OebChairpersonDraft extends Model
{
    use HasFactory;

    protected $table = 'pts3_oeb_chairperson_drafts';

    protected $guarded = [];

    public function pts3Draft(): BelongsTo
    {
        return $this->belongsTo(Pts3Draft::class);
    }
}
