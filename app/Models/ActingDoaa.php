<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActingDoaa extends Model
{
    use HasFactory;

    protected $table = 'acting_doaa';

    protected $fillable = [
        'user_id',
        'is_acting_doaa',
    ];

    protected function casts(): array
    {
        return [
            'is_acting_doaa' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
