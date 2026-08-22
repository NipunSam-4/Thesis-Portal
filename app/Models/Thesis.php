<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Thesis extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function draftSynopsisCirculation(): HasOne
    {
        return $this->hasOne(DraftSynopsisCirculation::class)->latestOfMany();
    }

    public function pts1Form(): HasOne
    {
        return $this->hasOne(Pts1Form::class)->latestOfMany();
    }

    public function pts1Forms(): HasMany
    {
        return $this->hasMany(Pts1Form::class);
    }

    public function pts2Form(): HasOne
    {
        return $this->hasOne(Pts2Form::class)->latestOfMany();
    }

    public function pts2Forms(): HasMany
    {
        return $this->hasMany(Pts2Form::class);
    }

    public function pts2Extension(): HasOne
    {
        return $this->hasOne(Pts2Extension::class)->latestOfMany();
    }

    public function pts2Extensions(): HasMany
    {
        return $this->hasMany(Pts2Extension::class);
    }

    public function pts3Form(): HasOne
    {
        return $this->hasOne(Pts3Form::class)->latestOfMany();
    }

    public function pts3Forms(): HasMany
    {
        return $this->hasMany(Pts3Form::class);
    }

    public function pts4Form(): HasOne
    {
        return $this->hasOne(Pts4Form::class)->latestOfMany();
    }

    public function pts4Forms(): HasMany
    {
        return $this->hasMany(Pts4Form::class);
    }

    public function pts5Form(): HasOne
    {
        return $this->hasOne(Pts5Form::class)->latestOfMany();
    }

    public function pts5Forms(): HasMany
    {
        return $this->hasMany(Pts5Form::class);
    }

    public function pts6Form(): HasOne
    {
        return $this->hasOne(Pts6Form::class)->latestOfMany();
    }

    public function pts6Forms(): HasMany
    {
        return $this->hasMany(Pts6Form::class);
    }


    // Accessor to get human-readable formatted status (e.g. 'in_progress' -> 'In Progress').
    public function getCurrentStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress','approved' => 'Approved',
            'rejected' => 'Rejected',
            'reverted' => 'Reverted',
            default => 'Pending',
        };
    }

    // Accessor to dynamically determine the currently active form ('pts1', 'pts2', etc.).
    public function getActiveFormAttribute(): string
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return 'pts1';
        }
        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return 'pts2';
        }
        if (!$this->pts3Form || $this->pts3Form->status !== 'approved') {
            return 'pts3';
        }
        if (!$this->pts4Form || $this->pts4Form->status !== 'approved') {
            return 'pts4';
        }
        if (!$this->pts5Form || $this->pts5Form->status !== 'approved') {
            return 'pts5';
        }
        if (!$this->pts6Form || $this->pts6Form->status !== 'approved') {
            return 'pts6';
        }
        return 'completed';
    }

    // Accessor to get the human-readable stage of the active form.
    public function getActiveStageLabelAttribute(): string
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return $this->pts1Form ? $this->pts1Form->stage_label : 'PTS-1 Not Submitted';
        }
        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return $this->pts2Form ? $this->pts2Form->stage_label : 'PTS-2 Not Submitted';
        }
        if (!$this->pts3Form || $this->pts3Form->status !== 'approved') {
            return $this->pts3Form ? $this->pts3Form->stage_label : 'PTS-3 Not Submitted';
        }
        if (!$this->pts4Form || $this->pts4Form->status !== 'approved') {
            return $this->pts4Form ? $this->pts4Form->stage_label : 'PTS-4 Not Submitted';
        }
        if (!$this->pts5Form || $this->pts5Form->status !== 'approved') {
            return $this->pts5Form ? $this->pts5Form->stage_label : 'PTS-5 Not Submitted';
        }
        if (!$this->pts6Form || $this->pts6Form->status !== 'approved') {
            return $this->pts6Form ? $this->pts6Form->stage_label : 'PTS-6 Not Submitted';
        }
        return 'Thesis Workflow Completed';
    }

    // Get Open Seminar date from PTS-1 form.
    public function getOpenSeminarDate(): ?\Carbon\Carbon
    {
        return $this->pts1Form?->seminar_date ? \Carbon\Carbon::parse($this->pts1Form->seminar_date)->startOfDay() : null;
    }

    // Min extension date: max(last approved extension date, 16 days from open seminar date).
    public function getMinExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $minDate = $seminarDate ? $seminarDate->copy()->addDays(16)->startOfDay() : null;

        $lastApprovedExtension = $this->pts2Extensions()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if ($lastApprovedExtension) {
            $approvedDate = $lastApprovedExtension->approved_extended_until_date 
                ? \Carbon\Carbon::parse($lastApprovedExtension->approved_extended_until_date)->startOfDay()
                : ($lastApprovedExtension->extended_until_date ? \Carbon\Carbon::parse($lastApprovedExtension->extended_until_date)->startOfDay() : null);

            if ($approvedDate && (!$minDate || $approvedDate->gt($minDate))) {
                $minDate = $approvedDate;
            }
        }

        return $minDate;
    }

    // Max extension date: 30 days from open seminar date.
    public function getMaxExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        return $seminarDate ? $seminarDate->copy()->addDays(30)->startOfDay() : null;
    }

    // Check if student can apply for PTS-2 extension (active up to 30 days after open seminar).
    public function canApplyForPts2Extension(): bool
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return false;
        }

        $seminarDate = $this->getOpenSeminarDate();
        if (!$seminarDate) {
            return false;
        }

        $extensionDeadline = $seminarDate->copy()->addDays(30)->endOfDay();
        return now()->lte($extensionDeadline);
    }

    // Get PTS-2 submission deadline date.
    // Default: 16 days from open seminar date.
    // If extension is approved: extended date.
    public function getPts2Deadline(): ?\Carbon\Carbon
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return null;
        }

        $extension = $this->pts2Extension;
        if ($extension && $extension->status === 'approved') {
            $extendedDate = $extension->approved_extended_until_date ?? $extension->extended_until_date;
            if ($extendedDate) {
                return \Carbon\Carbon::parse($extendedDate)->endOfDay();
            }
        }

        $seminarDate = $this->getOpenSeminarDate();
        return $seminarDate ? $seminarDate->copy()->addDays(16)->endOfDay() : null;
    }

    // Check if PTS-2 form button/submission is active.
    // Active till 16 days from open seminar unless extension is approved (then active till extended date).
    public function isPts2SubmissionActive(): bool
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return false;
        }

        $deadline = $this->getPts2Deadline();
        if (!$deadline) {
            return false;
        }

        return now()->lte($deadline);
    }
}