<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pts3ExaminerDraft extends Model
{
    use HasFactory;

    protected $table = 'pts3_examiner_drafts';

    protected $guarded = [];

    public function pts3Draft(): BelongsTo
    {
        return $this->belongsTo(Pts3Draft::class);
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
