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

    // Min extension date: max(last approved extension date, normal_deadline_days from open seminar date).
    public function getMinExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $normalDays = (int) SystemSetting::get('pts2_normal_deadline_days', 16);
        $minDate = $seminarDate ? $seminarDate->copy()->addDays($normalDays)->startOfDay() : null;

        $lastApprovedExtension = $this->pts2Extensions()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if ($lastApprovedExtension && $lastApprovedExtension->approved_extended_until_date) {
            $approvedDate = \Carbon\Carbon::parse($lastApprovedExtension->approved_extended_until_date)->startOfDay();

            if (!$minDate || $approvedDate->gt($minDate)) {
                $minDate = $approvedDate;
            }
        }

        return $minDate;
    }

    // Max extension date: max_extension_days from open seminar date.
    public function getMaxExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $maxDays = (int) SystemSetting::get('pts2_max_extension_days', 30);
        return $seminarDate ? $seminarDate->copy()->addDays($maxDays)->startOfDay() : null;
    }

    // Check if student can apply for PTS-2 extension (active up to max_extension_days after open seminar).
    public function canApplyForPts2Extension(): bool
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return false;
        }

        $seminarDate = $this->getOpenSeminarDate();
        if (!$seminarDate) {
            return false;
        }

        $maxDays = (int) SystemSetting::get('pts2_max_extension_days', 30);
        $extensionDeadline = $seminarDate->copy()->addDays($maxDays)->endOfDay();
        return now()->lte($extensionDeadline);
    }

    // Get PTS-2 submission deadline date.
    // Default: normal_deadline_days from open seminar date.
    // If extension is approved: extended date.
    public function getPts2Deadline(): ?\Carbon\Carbon
    {
        if (!$this->pts1Form || $this->pts1Form->status !== 'approved') {
            return null;
        }

        $extension = $this->pts2Extension;
        if ($extension && $extension->status === 'approved') {
            $extendedDate = $extension->approved_extended_until_date;
            if ($extendedDate) {
                return \Carbon\Carbon::parse($extendedDate)->endOfDay();
            }
        }

        $seminarDate = $this->getOpenSeminarDate();
        $normalDays = (int) SystemSetting::get('pts2_normal_deadline_days', 16);
        return $seminarDate ? $seminarDate->copy()->addDays($normalDays)->endOfDay() : null;
    }

    // Check if PTS-2 form button/submission is active.
    // Active till normal_deadline_days from open seminar unless extension is approved (then active till extended date).
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
    // PTS-4 EXTENSION HELPERS
    // ==========================================

    // Min PTS-4 extension date: max(last approved extension date, normal_deadline_days + 1 from open seminar date).
    public function getMinPts4ExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $normalDays = (int) SystemSetting::get('pts4_normal_deadline_days', 30);
        $minDate = $seminarDate ? $seminarDate->copy()->addDays($normalDays + 1)->startOfDay() : null;

        $lastApprovedExtension = $this->pts4Extensions()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if ($lastApprovedExtension && $lastApprovedExtension->approved_extended_until_date) {
            $approvedDate = \Carbon\Carbon::parse($lastApprovedExtension->approved_extended_until_date)->startOfDay();

            if (!$minDate || $approvedDate->gt($minDate)) {
                $minDate = $approvedDate;
            }
        }

        return $minDate;
    }

    // Max PTS-4 extension date: max_extension_days from open seminar date.
    public function getMaxPts4ExtensionDate(): ?\Carbon\Carbon
    {
        $seminarDate = $this->getOpenSeminarDate();
        $maxDays = (int) SystemSetting::get('pts4_max_extension_days', 60);
        return $seminarDate ? $seminarDate->copy()->addDays($maxDays)->startOfDay() : null;
    }

    // Check if student can apply for PTS-4 extension (active up to max_extension_days after open seminar).
    public function canApplyForPts4Extension(): bool
    {
        // Student must have PTS-2 approved before submitting PTS-4 / PTS-4 extension
        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return false;
        }

        $seminarDate = $this->getOpenSeminarDate();
        if (!$seminarDate) {
            return false;
        }

        $maxDays = (int) SystemSetting::get('pts4_max_extension_days', 60);
        $extensionDeadline = $seminarDate->copy()->addDays($maxDays)->endOfDay();
        return now()->lte($extensionDeadline);
    }

    // Get PTS-4 submission deadline date.
    // Default: normal_deadline_days from open seminar date.
    // If extension is approved: extended date.
    public function getPts4Deadline(): ?\Carbon\Carbon
    {
        if (!$this->pts2Form || $this->pts2Form->status !== 'approved') {
            return null;
        }

        $extension = $this->pts4Extension;
        if ($extension && $extension->status === 'approved') {
            $extendedDate = $extension->approved_extended_until_date;
            if ($extendedDate) {
                return \Carbon\Carbon::parse($extendedDate)->endOfDay();
            }
        }

        $seminarDate = $this->getOpenSeminarDate();
        $normalDays = (int) SystemSetting::get('pts4_normal_deadline_days', 30);
        return $seminarDate ? $seminarDate->copy()->addDays($normalDays)->endOfDay() : null;
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
        'dpgc'            => 'DPGC Convener',
        'hod'             => 'HOD',
        'academic_office'    => 'Academic Office',
        'dr'                 => 'Deputy Registrar (DR)',
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

        if (!$user) {
            if ($role === 'main_supervisor') {
                $mainSupId = $form->main_supervisor_id ?? null;
                $user = $mainSupId ? User::find($mainSupId) : ($form->mainSupervisor ?? null);
            } elseif (str_starts_with($role, 'co_supervisor') && preg_match('/co_supervisor_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "co_supervisor_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
            } elseif (str_starts_with($role, 'pspc_member') && preg_match('/pspc_member_(\d+)/', $role, $matches)) {
                $idx = (int)$matches[1];
                $col = "pspc_member_{$idx}_id";
                $userId = $form->$col ?? null;
                $user = $userId ? User::find($userId) : null;
            } elseif ($role === 'dpgc') {
                $user = $form->dpgcUser ?? (!empty($form->dpgc_user_id) ? User::find($form->dpgc_user_id) : null);
            } elseif ($role === 'hod') {
                $user = $form->hodUser ?? (!empty($form->hod_user_id) ? User::find($form->hod_user_id) : null);
            } elseif ($role === 'academic_office') {
                $user = $form->academicOfficeUser ?? (!empty($form->academic_office_user_id) ? User::find($form->academic_office_user_id) : null);
            } elseif ($role === 'doaa' || $role === 'adoaa') {
                $user = $form->doaaUser ?? (!empty($form->doaa_user_id) ? User::find($form->doaa_user_id) : null);
            } elseif ($role === 'senate_chairperson') {
                $user = $form->senateChairpersonUser ?? (!empty($form->senate_chairperson_user_id) ? User::find($form->senate_chairperson_user_id) : null);
            }
        }

        if ($role === 'main_supervisor') {
            $name = $user?->name;
            return 'Main Supervisor' . ($name ? " ({$name})" : '');
        }

        if (str_starts_with($role, 'co_supervisor')) {
            $isExternal = $user?->isExternalSupervisor();
            $roleTitle = $isExternal ? 'External Supervisor' : 'Co-Supervisor';
            $coName = $user?->name;
            $inst = ($isExternal && $user->externalSupervisorProfile?->affiliated_institute)
                ? ' - ' . $user->externalSupervisorProfile->affiliated_institute
                : '';
            return $roleTitle . ($coName ? " ({$coName}{$inst})" : '');
        }

        if (str_starts_with($role, 'pspc_member')) {
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

        // Department & Global Authorities show designated official institutional titles
        if ($role === 'dpgc') {
            return [
                'role' => 'DPGC Convener',
                'name' => 'Department Post Graduate Committee Convener',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        if ($role === 'hod') {
            return [
                'role' => 'Head of Department',
                'name' => 'Head of Department',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        if ($role === 'academic_office') {
            return [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        if ($role === 'doaa' || $role === 'adoaa') {
            return [
                'role' => ($form instanceof Pts3Form) ? 'DOAA' : 'Dean of Academic Affairs',
                'name' => 'Dean of Academic Affairs',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        if ($role === 'dr') {
            return [
                'role' => 'Deputy Registrar (DR)',
                'name' => 'Deputy Registrar (Academic)',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        if ($role === 'senate_chairperson') {
            return [
                'role' => 'Senate Chairperson',
                'name' => 'Senate Chairperson',
                'institute' => null,
                'submitted_at' => $form->updated_at,
                'status_type' => 'reverted',
                'status_label' => '⚠️ Reverted',
            ];
        }

        // Resolve personal user name for Supervisors / PSPC members
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

    // Build the standardized submission timeline array for any PTS form or extension.
    public static function getSubmissionTimeline($form): array
    {
        if (!$form) {
            return [];
        }

        $timeline = [];
        $student = $form->thesis?->student;
        $deptId = $student?->department_id;
        $currentStage = $form->current_stage ?? '';
        $formStatus = $form->status ?? '';

        // 1. Student Submission (For forms initiated by student)
        if (!($form instanceof Pts3Form) && $form->created_at) {
            $timeline[] = [
                'role' => 'Student Submission',
                'name' => $student?->user?->name ?? 'Student',
                'submitted_at' => $form->created_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        }

        // 2. Main Supervisor
        $mainSupUser = $form->mainSupervisor 
            ?? (!empty($form->main_supervisor_id) ? User::find($form->main_supervisor_id) : ($student?->mainSupervisors?->first() ?? $student?->mainSupervisor));
        $mainSupSubmitted = ($form->main_supervisor_submitted_at && $form->main_supervisor_recommendation !== null);
        
        if ($mainSupSubmitted) {
            $timeline[] = [
                'role' => 'Main Supervisor',
                'name' => $mainSupUser?->name ?? 'Main Supervisor',
                'submitted_at' => $form->main_supervisor_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Submitted',
            ];
        } elseif ($formStatus === 'in_progress' && $currentStage === 'main_supervisor') {
            $timeline[] = [
                'role' => 'Main Supervisor',
                'name' => $mainSupUser?->name ?? 'Main Supervisor',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 3. Co-Supervisors (For forms with co-supervisors: PTS-1, PTS-2, PTS-3, PTS-4, DraftSynopsisCirculation)
        if (!($form instanceof Pts2Extension) && !($form instanceof Pts4Extension)) {
            $coSupervisors = $student?->allCoSupervisors() ?? collect();
            $hasCoSupervisors = $coSupervisors->count() > 0 || !empty($form->co_supervisor_1_id);
            if ($hasCoSupervisors) {
                $maxCo = max(1, $coSupervisors->count());
                for ($i = 1; $i <= 10; $i++) {
                    $coSup = $form->{"coSupervisor{$i}"} 
                        ?? (!empty($form->{"co_supervisor_{$i}_id"}) ? User::find($form->{"co_supervisor_{$i}_id"}) : $coSupervisors->get($i - 1));
                    if (!$coSup && $i > $maxCo) break;
                    if (!$coSup && empty($form->{"co_supervisor_{$i}_id"})) continue;

                    $roleLabel = ($student && $coSup) ? $student->getSupervisorRoleTitle($coSup) : "Co-Supervisor {$i}";
                    $nameLabel = $coSup?->name ?? "Co-Supervisor {$i}";
                    $institute = ($coSup?->isExternalSupervisor() && $coSup->externalSupervisorProfile?->affiliated_institute)
                        ? $coSup->externalSupervisorProfile->affiliated_institute
                        : null;

                    $submittedAt = $form->{"co_supervisor_{$i}_submitted_at"} 
                        ?? ($form->co_supervisors_submitted_at && $form->{"co_supervisor_{$i}_recommendation"} !== null ? $form->co_supervisors_submitted_at : null)
                        ?? ($form->{"co_supervisor_{$i}_recommendation"} !== null ? $form->updated_at : null);
                    $isSubmitted = ($form->{"co_supervisor_{$i}_recommendation"} !== null && $submittedAt);

                    if ($isSubmitted) {
                        $timeline[] = [
                            'role' => $roleLabel,
                            'name' => $nameLabel,
                            'institute' => $institute,
                            'submitted_at' => $submittedAt,
                            'status_type' => 'submitted',
                            'status_label' => '✓ Submitted',
                        ];
                    } elseif ($formStatus === 'in_progress' && $currentStage === 'co_supervisors' && $coSup) {
                        $timeline[] = [
                            'role' => $roleLabel,
                            'name' => $nameLabel,
                            'institute' => $institute,
                            'submitted_at' => null,
                            'status_type' => 'pending',
                            'status_label' => '⏳ Pending',
                        ];
                    }
                }
            }
        }

        // 4. PSPC Members (PTS-1 & DraftSynopsisCirculation)
        if ($form instanceof Pts1Form || $form instanceof DraftSynopsisCirculation) {
            $pspcMembers = $student?->pspcMembers ?? collect();
            $hasPspc = $pspcMembers->count() > 0 || !empty($form->pspc_member_1_id);
            if ($hasPspc) {
                $maxPspc = max(1, $pspcMembers->count());
                for ($i = 1; $i <= 10; $i++) {
                    $pspc = $form->{"pspcMember{$i}"} 
                        ?? (!empty($form->{"pspc_member_{$i}_id"}) ? User::find($form->{"pspc_member_{$i}_id"}) : $pspcMembers->get($i - 1));
                    if (!$pspc && $i > $maxPspc) break;
                    if (!$pspc && empty($form->{"pspc_member_{$i}_id"})) continue;

                    $submittedAt = $form->{"pspc_member_{$i}_submitted_at"} 
                        ?? ($form->pspc_members_submitted_at && $form->{"pspc_member_{$i}_recommendation"} !== null ? $form->pspc_members_submitted_at : null)
                        ?? ($form->{"pspc_member_{$i}_recommendation"} !== null ? $form->updated_at : null);
                    $isSubmitted = ($form->{"pspc_member_{$i}_recommendation"} !== null && $submittedAt);

                    if ($isSubmitted) {
                        $timeline[] = [
                            'role' => 'PSPC Member',
                            'name' => $pspc?->name ?? "PSPC Member {$i}",
                            'submitted_at' => $submittedAt,
                            'status_type' => 'submitted',
                            'status_label' => '✓ Submitted',
                        ];
                    } elseif ($formStatus === 'in_progress' && $currentStage === 'pspc_members' && $pspc) {
                        $timeline[] = [
                            'role' => 'PSPC Member',
                            'name' => $pspc->name ?? "PSPC Member {$i}",
                            'submitted_at' => null,
                            'status_type' => 'pending',
                            'status_label' => '⏳ Pending',
                        ];
                    }
                }
            }
        }

        // 5. DPGC (Present in PTS-1, PTS-3, PTS-2 Extension, PTS-4 Extension, DraftSynopsisCirculation)
        if (!($form instanceof Pts2Form) && !($form instanceof Pts4Form)) {
            if ($form->dpgc_submitted_at) {
                $timeline[] = [
                    'role' => 'DPGC Convener',
                    'name' => 'Department Post Graduate Committee Convener',
                    'submitted_at' => $form->dpgc_submitted_at,
                    'status_type' => 'submitted',
                    'status_label' => '✓ Submitted',
                ];
            } elseif ($formStatus === 'in_progress' && $currentStage === 'dpgc') {
                $timeline[] = [
                    'role' => 'DPGC Convener',
                    'name' => 'Department Post Graduate Committee Convener',
                    'submitted_at' => null,
                    'status_type' => 'pending',
                    'status_label' => '⏳ Pending',
                ];
            }
        }

        // 6. HOD (Present in PTS-1, PTS-3, PTS-2 Extension, PTS-4 Extension, DraftSynopsisCirculation)
        if (!($form instanceof Pts2Form) && !($form instanceof Pts4Form)) {
            if ($form->hod_submitted_at) {
                $timeline[] = [
                    'role' => 'Head of Department',
                    'name' => 'Head of Department',
                    'submitted_at' => $form->hod_submitted_at,
                    'status_type' => 'submitted',
                    'status_label' => '✓ Submitted',
                ];
            } elseif ($formStatus === 'in_progress' && $currentStage === 'hod') {
                $timeline[] = [
                    'role' => 'Head of Department',
                    'name' => 'Head of Department',
                    'submitted_at' => null,
                    'status_type' => 'pending',
                    'status_label' => '⏳ Pending',
                ];
            }
        }

        // 7. Academic Office (All forms)
        if ($form->academic_office_submitted_at) {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'submitted_at' => $form->academic_office_submitted_at,
                'status_type' => 'submitted',
                'status_label' => '✓ Verified',
            ];
        } elseif ($formStatus === 'in_progress' && $currentStage === 'academic_office') {
            $timeline[] = [
                'role' => 'Academic Office',
                'name' => 'Academic Office',
                'submitted_at' => null,
                'status_type' => 'pending',
                'status_label' => '⏳ Pending',
            ];
        }

        // 8. DOAA (PTS-1, PTS-2, PTS-3, PTS-2 Extension, PTS-4 Extension)
        if (!($form instanceof Pts4Form)) {
            if ($form->doaa_submitted_at) {
                $isPts3 = ($form instanceof Pts3Form);
                $isApproved = ($form->doaa_approval || $formStatus === 'approved');
                
                $timeline[] = [
                    'role' => $isPts3 ? 'DOAA' : 'Dean of Academic Affairs',
                    'name' => 'Dean of Academic Affairs',
                    'submitted_at' => $form->doaa_submitted_at,
                    'status_type' => $isPts3 ? 'submitted' : ($isApproved ? 'approved' : 'rejected'),
                    'status_label' => $isPts3 ? '✓ Submitted' : ($isApproved ? '✓ Approved' : '❌ Rejected'),
                ];
            } elseif ($formStatus === 'in_progress' && $currentStage === 'doaa') {
                $timeline[] = [
                    'role' => ($form instanceof Pts3Form) ? 'DOAA' : 'Dean of Academic Affairs',
                    'name' => 'Dean of Academic Affairs',
                    'submitted_at' => null,
                    'status_type' => 'pending',
                    'status_label' => '⏳ Pending',
                ];
            }
        }

        // 9. Deputy Registrar (DR) (PTS-4 only)
        if ($form instanceof Pts4Form) {
            if ($form->dr_submitted_at) {
                $isApproved = ($form->dr_approval || $formStatus === 'approved');

                $timeline[] = [
                    'role' => 'Deputy Registrar (DR)',
                    'name' => 'Deputy Registrar (Academic)',
                    'submitted_at' => $form->dr_submitted_at,
                    'status_type' => $isApproved ? 'approved' : 'rejected',
                    'status_label' => $isApproved ? '✓ Approved' : '❌ Rejected',
                ];
            } elseif ($formStatus === 'in_progress' && $currentStage === 'dr') {
                $timeline[] = [
                    'role' => 'Deputy Registrar (DR)',
                    'name' => 'Deputy Registrar (Academic)',
                    'submitted_at' => null,
                    'status_type' => 'pending',
                    'status_label' => '⏳ Pending',
                ];
            }
        }

        // 10. Senate Chairperson (PTS-3 only)
        if ($form instanceof Pts3Form) {
            if ($form->senate_chairperson_submitted_at) {
                $isApproved = ($form->senate_chairperson_approval || $formStatus === 'approved');

                $timeline[] = [
                    'role' => 'Senate Chairperson',
                    'name' => 'Chairperson, Senate',
                    'submitted_at' => $form->senate_chairperson_submitted_at,
                    'status_type' => $isApproved ? 'approved' : 'rejected',
                    'status_label' => $isApproved ? '✓ Approved' : '❌ Rejected',
                ];
            } elseif ($formStatus === 'in_progress' && $currentStage === 'senate_chairperson') {
                $timeline[] = [
                    'role' => 'Senate Chairperson',
                    'name' => 'Chairperson, Senate',
                    'submitted_at' => null,
                    'status_type' => 'pending',
                    'status_label' => '⏳ Pending',
                ];
            }
        }

        // 11. Reverted Step (if form is reverted)
        if ($formStatus === 'reverted') {
            $revertItem = self::getRevertedTimelineItem($form);
            if ($revertItem) {
                $timeline[] = $revertItem;
            }
        }

        return $timeline;
    }

    /**
     * Determine an evaluating authority's workflow role in relation to an extension application.
     */
    public static function determineExtensionUserRole(?User $user, $extension): ?string
    {
        if (!$user || !$extension) {
            return null;
        }

        $student = $extension->thesis?->student;

        if ($student && $student->isMainSupervisor($user)) {
            return 'main_supervisor';
        }

        if ($user->isDpgc() && (!$student || $user->deptAuthorityProfile?->department_id === $student->department_id)) {
            return 'dpgc';
        }

        if ($user->isHod() && (!$student || $user->deptAuthorityProfile?->department_id === $student->department_id)) {
            return 'hod';
        }

        if ($user->isAcademicOffice()) {
            return 'academic_office';
        }

        if ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($extension->acting_doaa_email === $user->email || $extension->vested_doaa_email === $user->email))) {
            return 'doaa';
        }

        return null;
    }
}