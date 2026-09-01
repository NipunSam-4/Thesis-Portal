<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VestedDoaa extends Model
{
    use HasFactory;

    protected $table = 'vested_doaa';

    protected $fillable = [
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getActiveVestedUser(): ?User
    {
        $vested = self::where('is_active', true)->with('user')->first();
        return $vested ? $vested->user : null;
    }

    public static function getActiveVestedEmail(): ?string
    {
        $user = self::getActiveVestedUser();
        return $user ? $user->email : null;
    }
}
