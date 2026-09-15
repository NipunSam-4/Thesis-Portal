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

    protected $casts = [];

    public function pts3Form(): BelongsTo
    {
        return $this->belongsTo(Pts3Form::class);
    }

    public function getFormattedPhoneNumber(): string
    {
        if (!$this->phone_number) {
            return 'N/A';
        }
        $code = $this->phone_country_code ?: '+91';
        return trim("{$code} {$this->phone_number}");
    }
}
