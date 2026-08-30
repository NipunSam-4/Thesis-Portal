<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalSupervisorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'affiliated_institute',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
