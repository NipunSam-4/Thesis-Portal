<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class DraftSynopsisCirculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'thesis_title',
        'draft_synopsis_doc_path',
        'status',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DraftSynopsisComment::class);
    }

    // Get comments ordered by authority hierarchy.
    public function getOrderedComments()
    {
        $comments = $this->comments()->with('user')->get();

        // Standard authority hierarchy weight mapping
        $roleWeights = [
            'main_supervisor' => 1,
            'co_supervisor' => 2,
            'pspc_member' => 3,
            'dpgc' => 4,
            'hod' => 5,
            'academic_office' => 6,
            'doaa' => 7,
        ];

        return $comments->sortBy(function ($comment) use ($roleWeights) {
            $baseWeight = $roleWeights[$comment->authority_role] ?? 99;
            return $baseWeight * 1000 + ($comment->id % 1000);
        })->values();
    }

    // Get array of completed submission timestamps for all reviewing authorities and student.
    public function getSubmittedTimeline(): array
    {
        $timeline = [];
        $student = $this->thesis?->student;

        if ($this->created_at) {
            $timeline[] = [
                'role' => 'Student Circulation',
                'name' => $student?->user?->name ?? 'Student',
                'submitted_at' => $this->created_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Circulated',
            ];
        }

        foreach ($this->comments as $comment) {
            $roleLabel = match ($comment->authority_role) {
                'main_supervisor' => 'Main Supervisor',
                'co_supervisor' => 'Co-Supervisor',
                'pspc_member' => 'PSPC Member',
                'dpgc' => 'DPGC Convener',
                'hod' => 'Head of Department',
                'academic_office' => 'Academic Office',
                'doaa' => 'Dean of Academic Affairs',
                default => str_replace('_', ' ', ucfirst($comment->authority_role ?? '')),
            };

            $user = $comment->user;
            $institute = ($user?->isExternalSupervisor() && $user->externalSupervisorProfile?->affiliated_institute)
                ? $user->externalSupervisorProfile->affiliated_institute
                : null;

            $timeline[] = [
                'role' => $roleLabel . ' Review',
                'name' => $user?->name ?? $roleLabel,
                'institute' => $institute,
                'submitted_at' => $comment->created_at,
                'status_type' => 'submitted',
                'status_label' => '💬 Commented',
            ];
        }
        return $timeline;
    }

    public function canUserView(?User $user): bool
    {
        if (!$user) return false;
        $student = $this->thesis?->student;
        if (!$student) return false;
        return $student->isSupervisor($user) || $student->isPspcMember($user);
    }
}

