<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CommentSnippet extends Model
{
    use HasFactory;

    public const ROLES_DEPT = ['dpgc', 'hod'];
    public const ROLES_GLOBAL = ['academic_office', 'ar', 'dr', 'doaa', 'senate_chairperson'];

    public const FORM_TYPES = [
        'pts1',
        'pts2',
        'pts2_extension',
        'pts3',
        'pts4',
        'pts5',
        'pts6',
        'all',
    ];

    public const COMMENT_TYPES = [
        'confidential',
        'student_comment',
        'verification_remark',
    ];

    protected $fillable = [
        'content',
        'form_type',
        'comment_type',
        'role',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Resolve applicable roles for query based on hierarchy
    public static function resolveRolesForQuery(string $role): array
    {
        $normalizedRole = strtolower(trim($role));

        if (in_array($normalizedRole, self::ROLES_DEPT, true)) {
            return [$normalizedRole, 'all_dept'];
        }

        if (in_array($normalizedRole, self::ROLES_GLOBAL, true)) {
            return [$normalizedRole, 'all_global'];
        }

        if ($normalizedRole === 'all_dept') {
            return ['all_dept'];
        }

        if ($normalizedRole === 'all_global') {
            return ['all_global'];
        }

        return [$normalizedRole];
    }

    // Primary Scope: retrieve snippets matching form, role hierarchy, and comment type
    public function scopeForSnippet(
        Builder $query,
        string $formType = 'all',
        string $role = 'all',
        ?string $commentType = null
    ): Builder {
        $query->where('is_active', true);

        // Form Type Filter
        if ($formType && $formType !== 'all') {
            $query->where(function ($q) use ($formType) {
                $q->where('form_type', $formType)
                  ->orWhere('form_type', 'all')
                  ->orWhereNull('form_type');
            });
        }

        // Role Filter (with departmental/global inheritance)
        if ($role && $role !== 'all') {
            $roles = static::resolveRolesForQuery($role);
            $query->where(function ($q) use ($roles) {
                $q->whereIn('role', $roles)
                  ->orWhereNull('role');
            });
        }

        // Comment Type Filter
        if ($commentType && $commentType !== 'all') {
            $query->where('comment_type', $commentType);
        }

        return $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }
}
