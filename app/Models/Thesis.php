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

    public function pts4Extension(): HasOne
    {
        return $this->hasOne(Pts4Extension::class)->latestOfMany();
    }

    public function pts4Extensions(): HasMany
    {
        return $this->hasMany(Pts4Extension::class);
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


    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function markCompletedIfFinished(): void
    {
        if (
            $this->pts1Form?->status === 'approved' &&
            $this->pts2Form?->status === 'approved' &&
            $this->pts3Form?->status === 'approved' &&
            $this->pts4Form?->status === 'approved' &&
            $this->pts5Form?->status === 'approved' &&
            $this->pts6Form?->status === 'approved'
        ) {
            $this->update(['status' => 'completed']);
        }
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

    // ==========================================
    // PTS-4 EXTENSION HELPERS (31 to 60 days post Open Seminar)
    // ==========================================

    // Min PTS-4 extension date: max(last approved extension date, 31 days from open seminar date).
    public function getMinPts4ExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $minDate = $seminarDate ? $seminarDate->copy()->addDays(31)->startOfDay() : null;

        $lastApprovedExtension = $this->pts4Extensions()
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

    // Max PTS-4 extension date: 60 days from open seminar date.
    public function getMaxPts4ExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        return $seminarDate ? $seminarDate->copy()->addDays(60)->startOfDay() : null;
    }

    // Check if student can apply for PTS-4 extension (active from 31 up to 60 days after open seminar).
    public function canApplyForPts4Extension(): bool
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return false;
        }

        // Student must have PTS-2 approved before submitting PTS-4 / PTS-4 extension
        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return false;
        }

        $seminarDate = $this->getOpenSeminarDate();
        if (!$seminarDate) {
            return false;
        }

        $extensionDeadline = $seminarDate->copy()->addDays(60)->endOfDay();
        return now()->lte($extensionDeadline);
    }

    // Get PTS-4 submission deadline date.
    // Default: 30 days from open seminar date.
    // If extension is approved: extended date.
    public function getPts4Deadline(): ?\Carbon\Carbon
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return null;
        }

        $extension = $this->pts4Extension;
        if ($extension && $extension->status === 'approved') {
            $extendedDate = $extension->approved_extended_until_date ?? $extension->extended_until_date;
            if ($extendedDate) {
                return \Carbon\Carbon::parse($extendedDate)->endOfDay();
            }
        }

        $seminarDate = $this->getOpenSeminarDate();
        return $seminarDate ? $seminarDate->copy()->addDays(30)->endOfDay() : null;
    }

    // Check if PTS-4 form button/submission is active.
    public function isPts4SubmissionActive(): bool
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return false;
        }

        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return false;
        }

        $deadline = $this->getPts4Deadline();
        if (!$deadline) {
            return false;
        }

        return now()->lte($deadline);
    }

    // Single unified stage mapping for all forms across the entire portal.
    public static array $stageMappings = [
        'main_supervisor' => 'Main Supervisor',
        'co_supervisors'  => 'Co-Supervisors',
        'pspc_members'    => 'PSPC Members',
        'dpgc'            => 'DPGC',
        'hod'             => 'HOD',
        'academic_office'    => 'Academic Office',
        'adoaa'              => 'ADoAA',
        'doaa'            => 'DOAA',
        'senate_chairperson' => 'Senate Chairperson',
        'completed'       => 'Approved',
        'rejected'        => 'Rejected',
        'reverted'        => 'Reverted',
    ];

    // Single unified status mapping for all forms and extensions.
    public static array $statusMappings = [
        'in_progress'   => 'In Progress',
        'approved'      => 'Approved',
        'rejected'      => 'Rejected',
        'reverted'      => 'Reverted',
        'pending'       => 'Pending',
    ];

    // Convert raw stage slug to human-readable label.
    public static function getStageLabel(string $stage): string
    {
        return self::$stageMappings[$stage] ?? ucwords(str_replace('_', ' ', $stage));
    }

    // Convert raw status slug to human-readable label.
    public static function getStatusLabel(?string $status): string
    {
        if (!$status) {
            return '';
        }
        return self::$statusMappings[$status] ?? ucwords(str_replace('_', ' ', $status));
    }

    // Get human-readable role label for the authority who reverted the form, including user name.
    public static function getRevertedByRoleLabel($form): string
    {
        if (!$form) {
            return 'Academic Authority';
        }

        $role = $form->reverted_by_role ?? '';
        if (!$role) {
            return 'Academic Authority';
        }

        // 1. Resolve user strictly from form's reverted_by_id or form relationships
        $user = $form->revertedBy ?? null;
        if (!$user && !empty($form->reverted_by_id)) {
            $user = User::find($form->reverted_by_id);
        }

        if ($role === 'main_supervisor') {
            if (!$user) {
                $mainSupId = $form->main_supervisor_id ?? null;
                $user = $mainSupId ? User::find($mainSupId) : ($form->mainSupervisor ?? null);
            }
            $name = $user?->name;
            return 'Main Supervisor' . ($name ? " ({$name})" : '');
        }

        if (str_starts_with($role, 'co_supervisor')) {
            if (!$user && preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
            }
            $isExternal = $user?->isExternalSupervisor();
            $roleTitle = $isExternal ? 'External Supervisor' : 'Co-Supervisor';
            $coName = $user?->name;
            $inst = ($isExternal && $user->externalSupervisorProfile?->affiliated_institute)
                ? ' - ' . $user->externalSupervisorProfile->affiliated_institute
                : '';
            return $roleTitle . ($coName ? " ({$coName}{$inst})" : '');
        }

        if (str_starts_with($role, 'pspc_member')) {
            if (!$user && preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
            }
            $name = $user?->name;
            return 'PSPC Member' . ($name ? " ({$name})" : '');
        }

        $roleLabel = self::getStageLabel($role);
        $name = $user?->name;
        return $roleLabel . ($name ? " ({$name})" : '');
    }

    // Get the standardized timeline item for a reverted form.
    public static function getRevertedTimelineItem($form): ?array
    {
        if (!$form || $form->status !== 'reverted' || (!$form->reverted_by_role && !$form->reversion_comment)) {
            return null;
        }

        $role = $form->reverted_by_role ?? '';

        // 1. Resolve user strictly from form's reverted_by_id or form relationships
        $revertingUser = $form->revertedBy ?? null;
        if (!$revertingUser && !empty($form->reverted_by_id)) {
            $revertingUser = User::find($form->reverted_by_id);
        }

        if (!$revertingUser) {
            if ($role === 'main_supervisor') {
                $mainSupId = $form->main_supervisor_id ?? null;
                $revertingUser = $mainSupId ? User::find($mainSupId) : ($form->mainSupervisor ?? null);
            } elseif (str_starts_with($role, 'co_supervisor') && preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $form->$col ?? null;
                $revertingUser = $userId ? User::find($userId) : null;
            } elseif (str_starts_with($role, 'pspc_member') && preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $form->$col ?? null;
                $revertingUser = $userId ? User::find($userId) : null;
            }
        }

        if ($role === 'main_supervisor') {
            $revertRole = 'Main Supervisor';
        } elseif (str_starts_with($role, 'co_supervisor')) {
            $revertRole = $revertingUser?->isExternalSupervisor() ? 'External Supervisor' : 'Co-Supervisor';
        } elseif (str_starts_with($role, 'pspc_member')) {
            $revertRole = 'PSPC Member';
        } else {
            $revertRole = self::getStageLabel($role);
        }

        $revertName = $revertingUser?->name ?? 'Reverting Authority';
        $revertInstitute = ($revertingUser?->isExternalSupervisor() && $revertingUser->externalSupervisorProfile?->affiliated_institute)
            ? $revertingUser->externalSupervisorProfile->affiliated_institute
            : null;

        return [
            'role' => $revertRole,
            'name' => $revertName,
            'institute' => $revertInstitute,
            'submitted_at' => $form->updated_at,
            'status_type' => 'reverted',
            'status_label' => '⚠️ Reverted',
        ];
    }

    public function canUserViewDraftSynopsis(?User $user): bool
    {
        if (!$user) return false;
        $student = $this->student;
        if (!$student) return false;
        return $student->isSupervisor($user) || $student->isPspcMember($user);
    }
}