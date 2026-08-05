<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Examiner extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'name',
        'affiliation',
        'category', // 'Indian', 'Foreign'
        'contact_email',
        'consent_status', // 'Pending', 'Requested', 'Consented', 'Rejected'
        'view_order',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }
}