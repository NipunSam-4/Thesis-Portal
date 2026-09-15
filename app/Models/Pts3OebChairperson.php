<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts3OebChairperson extends Model
{
    use HasFactory;

    protected $table = 'pts3_oeb_chairpersons';

    protected $guarded = [];

    public function pts3Form(): BelongsTo
    {
        return $this->belongsTo(Pts3Form::class, 'pts3_form_id');
    }
}
