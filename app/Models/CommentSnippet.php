<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CommentSnippet extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'form_type',
        'role',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope snippets for a specific form type and role.
     */
    public function scopeForFormAndRole(Builder $query, string $formType = 'all', string $role = 'all'): Builder
    {
        $query->where('is_active', true);

        if ($formType !== 'all') {
            $query->where(function ($q) use ($formType) {
                $q->where('form_type', $formType)
                  ->orWhere('form_type', 'all')
                  ->orWhereNull('form_type');
            });
        }

        if ($role !== 'all') {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhere('role', 'all')
                  ->orWhereNull('role');
            });
        }

        return $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }
}
