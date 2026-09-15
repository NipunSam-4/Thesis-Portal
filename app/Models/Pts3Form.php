<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pts3Form extends Model
{
    use HasFactory;

    protected $table = 'pts3_forms';

    protected $fillable = [
    'thesis_id',
    'thesis_title',
    'current_stage',
    'status',
    'reverted_by_role',
    'reverted_by_id',
    'reversion_comment',

    // Main Supervisor
    'main_supervisor_recommendation',
    'main_supervisor_submitted_at',

    // Co-Supervisors (1 to 10)
    'co_supervisor_1_id', 'co_supervisor_1_recommendation', 'co_supervisor_1_submitted_at',
    'co_supervisor_2_id', 'co_supervisor_2_recommendation', 'co_supervisor_2_submitted_at',
    'co_supervisor_3_id', 'co_supervisor_3_recommendation', 'co_supervisor_3_submitted_at',
    'co_supervisor_4_id', 'co_supervisor_4_recommendation', 'co_supervisor_4_submitted_at',
    'co_supervisor_5_id', 'co_supervisor_5_recommendation', 'co_supervisor_5_submitted_at',
    'co_supervisor_6_id', 'co_supervisor_6_recommendation', 'co_supervisor_6_submitted_at',
    'co_supervisor_7_id', 'co_supervisor_7_recommendation', 'co_supervisor_7_submitted_at',
    'co_supervisor_8_id', 'co_supervisor_8_recommendation', 'co_supervisor_8_submitted_at',
    'co_supervisor_9_id', 'co_supervisor_9_recommendation', 'co_supervisor_9_submitted_at',
    'co_supervisor_10_id', 'co_supervisor_10_recommendation', 'co_supervisor_10_submitted_at',
    'co_supervisors_submitted_at',

    // DPGC & HOD
    'dpgc_recommendation',
    'dpgc_submitted_at',
    'hod_recommendation',
    'hod_submitted_at',

    // Academic Office
    'academic_office_is_verified',
    'academic_office_verification_remark',
    'academic_office_submitted_at',

    // DOAA
    'doaa_is_verified',
    'doaa_verification_remark',
    'doaa_submitted_at',

    // Senate Chairperson
    'senate_chairperson_approval',
    'senate_chairperson_confidential_remark',
    'senate_chairperson_approval_remark',
    'senate_chairperson_submitted_at',

    // Authority Snapshots & Vested DOAA Assignment
    'main_supervisor_id',
    'dpgc_user_id',
    'hod_user_id',
    'academic_office_user_id',
    'doaa_user_id',
    'senate_chairperson_user_id',
    'acting_doaa_email',
    'vested_doaa_email',
    'approved_by_id',

    // Indian Examiners (1 to 4)
    'indian_examiner_1_email', 'indian_examiner_1_has_consent', 'indian_examiner_1_consent_doc_path', 'indian_examiner_1_academic_office_remark', 'indian_examiner_1_doaa_remark', 'indian_examiner_1_doaa_priority', 'indian_examiner_1_senate_chairperson_priority',
    'indian_examiner_2_email', 'indian_examiner_2_has_consent', 'indian_examiner_2_consent_doc_path', 'indian_examiner_2_academic_office_remark', 'indian_examiner_2_doaa_remark', 'indian_examiner_2_doaa_priority', 'indian_examiner_2_senate_chairperson_priority',
    'indian_examiner_3_email', 'indian_examiner_3_has_consent', 'indian_examiner_3_consent_doc_path', 'indian_examiner_3_academic_office_remark', 'indian_examiner_3_doaa_remark', 'indian_examiner_3_doaa_priority', 'indian_examiner_3_senate_chairperson_priority',
    'indian_examiner_4_email', 'indian_examiner_4_has_consent', 'indian_examiner_4_consent_doc_path', 'indian_examiner_4_academic_office_remark', 'indian_examiner_4_doaa_remark', 'indian_examiner_4_doaa_priority', 'indian_examiner_4_senate_chairperson_priority',

    // International Examiners (1 to 4)
    'international_examiner_1_email', 'international_examiner_1_has_consent', 'international_examiner_1_consent_doc_path', 'international_examiner_1_academic_office_remark', 'international_examiner_1_doaa_remark', 'international_examiner_1_doaa_priority', 'international_examiner_1_senate_chairperson_priority',
    'international_examiner_2_email', 'international_examiner_2_has_consent', 'international_examiner_2_consent_doc_path', 'international_examiner_2_academic_office_remark', 'international_examiner_2_doaa_remark', 'international_examiner_2_doaa_priority', 'international_examiner_2_senate_chairperson_priority',
    'international_examiner_3_email', 'international_examiner_3_has_consent', 'international_examiner_3_consent_doc_path', 'international_examiner_3_academic_office_remark', 'international_examiner_3_doaa_remark', 'international_examiner_3_doaa_priority', 'international_examiner_3_senate_chairperson_priority',
    'international_examiner_4_email', 'international_examiner_4_has_consent', 'international_examiner_4_consent_doc_path', 'international_examiner_4_academic_office_remark', 'international_examiner_4_doaa_remark', 'international_examiner_4_doaa_priority', 'international_examiner_4_senate_chairperson_priority',

    // OEB Chairpersons (1 to 4)
    'oeb_chairperson_1_email', 'oeb_chairperson_1_academic_office_remark', 'oeb_chairperson_1_doaa_remark', 'oeb_chairperson_1_doaa_priority', 'oeb_chairperson_1_senate_chairperson_priority',
    'oeb_chairperson_2_email', 'oeb_chairperson_2_academic_office_remark', 'oeb_chairperson_2_doaa_remark', 'oeb_chairperson_2_doaa_priority', 'oeb_chairperson_2_senate_chairperson_priority',
    'oeb_chairperson_3_email', 'oeb_chairperson_3_academic_office_remark', 'oeb_chairperson_3_doaa_remark', 'oeb_chairperson_3_doaa_priority', 'oeb_chairperson_3_senate_chairperson_priority',
    'oeb_chairperson_4_email', 'oeb_chairperson_4_academic_office_remark', 'oeb_chairperson_4_doaa_remark', 'oeb_chairperson_4_doaa_priority', 'oeb_chairperson_4_senate_chairperson_priority',
    ];


    protected $casts = [
        'main_supervisor_recommendation' => 'boolean',
        'main_supervisor_submitted_at' => 'datetime',

        'co_supervisor_1_recommendation' => 'boolean',
        'co_supervisor_1_submitted_at' => 'datetime',
        'co_supervisor_2_recommendation' => 'boolean',
        'co_supervisor_2_submitted_at' => 'datetime',
        'co_supervisor_3_recommendation' => 'boolean',
        'co_supervisor_3_submitted_at' => 'datetime',
        'co_supervisor_4_recommendation' => 'boolean',
        'co_supervisor_4_submitted_at' => 'datetime',
        'co_supervisor_5_recommendation' => 'boolean',
        'co_supervisor_5_submitted_at' => 'datetime',
        'co_supervisor_6_recommendation' => 'boolean',
        'co_supervisor_6_submitted_at' => 'datetime',
        'co_supervisor_7_recommendation' => 'boolean',
        'co_supervisor_7_submitted_at' => 'datetime',
        'co_supervisor_8_recommendation' => 'boolean',
        'co_supervisor_8_submitted_at' => 'datetime',
        'co_supervisor_9_recommendation' => 'boolean',
        'co_supervisor_9_submitted_at' => 'datetime',
        'co_supervisor_10_recommendation' => 'boolean',
        'co_supervisor_10_submitted_at' => 'datetime',
        'co_supervisors_submitted_at' => 'datetime',

        'dpgc_recommendation' => 'boolean',
        'dpgc_submitted_at' => 'datetime',

        'hod_recommendation' => 'boolean',
        'hod_submitted_at' => 'datetime',

        'academic_office_is_verified' => 'boolean',
        'academic_office_submitted_at' => 'datetime',

        'doaa_is_verified' => 'boolean',
        'doaa_submitted_at' => 'datetime',

        'senate_chairperson_approval' => 'boolean',
        'senate_chairperson_submitted_at' => 'datetime',

        'indian_examiner_1_has_consent' => 'boolean',
        'indian_examiner_2_has_consent' => 'boolean',
        'indian_examiner_3_has_consent' => 'boolean',
        'indian_examiner_4_has_consent' => 'boolean',
        'international_examiner_1_has_consent' => 'boolean',
        'international_examiner_2_has_consent' => 'boolean',
        'international_examiner_3_has_consent' => 'boolean',
        'international_examiner_4_has_consent' => 'boolean',

        'indian_examiner_1_doaa_priority' => 'integer',
        'indian_examiner_2_doaa_priority' => 'integer',
        'indian_examiner_3_doaa_priority' => 'integer',
        'indian_examiner_4_doaa_priority' => 'integer',
        'indian_examiner_1_senate_chairperson_priority' => 'integer',
        'indian_examiner_2_senate_chairperson_priority' => 'integer',
        'indian_examiner_3_senate_chairperson_priority' => 'integer',
        'indian_examiner_4_senate_chairperson_priority' => 'integer',

        'international_examiner_1_doaa_priority' => 'integer',
        'international_examiner_2_doaa_priority' => 'integer',
        'international_examiner_3_doaa_priority' => 'integer',
        'international_examiner_4_doaa_priority' => 'integer',
        'international_examiner_1_senate_chairperson_priority' => 'integer',
        'international_examiner_2_senate_chairperson_priority' => 'integer',
        'international_examiner_3_senate_chairperson_priority' => 'integer',
        'international_examiner_4_senate_chairperson_priority' => 'integer',

        'oeb_chairperson_1_doaa_priority' => 'integer',
        'oeb_chairperson_2_doaa_priority' => 'integer',
        'oeb_chairperson_3_doaa_priority' => 'integer',
        'oeb_chairperson_4_doaa_priority' => 'integer',
        'oeb_chairperson_1_senate_chairperson_priority' => 'integer',
        'oeb_chairperson_2_senate_chairperson_priority' => 'integer',
        'oeb_chairperson_3_senate_chairperson_priority' => 'integer',
        'oeb_chairperson_4_senate_chairperson_priority' => 'integer',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(Pts3Examiner::class, 'pts3_form_id');
    }

    public function oebChairpersons(): HasMany
    {
        return $this->hasMany(Pts3OebChairperson::class, 'pts3_form_id');
    }

    /**
     * Get 4 Indian examiner objects with combined profile and form workflow data.
     */
    public function getIndianExaminers()
    {
        $profiles = $this->examiners->where('type', 'indian')->keyBy('email');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $email = $this->{"indian_examiner_{$i}_email"};
            if (!$email) {
                continue;
            }
            $profile = $profiles->get($email);
            $items->push((object)[
                'slot' => $i,
                'id' => $profile?->id ?? $i,
                'type' => 'indian',
                'name' => $profile?->name ?? '',
                'designation' => $profile?->designation ?? '',
                'organization' => $profile?->organization ?? '',
                'postal_address' => $profile?->postal_address ?? '',
                'email' => $email,
                'phone_number' => $profile?->phone_number ?? null,
                'phone_country_code' => $profile?->phone_country_code ?? '+91',
                'phone_iso2' => $profile?->phone_iso2 ?? 'in',
                'website' => $profile?->website ?? null,
                'research_area' => $profile?->research_area ?? null,
                'has_consent' => (bool)$this->{"indian_examiner_{$i}_has_consent"},
                'consent_doc_path' => $this->{"indian_examiner_{$i}_consent_doc_path"},
                'academic_office_remark' => $this->{"indian_examiner_{$i}_academic_office_remark"},
                'doaa_remark' => $this->{"indian_examiner_{$i}_doaa_remark"},
                'doaa_priority' => $this->{"indian_examiner_{$i}_doaa_priority"},
                'senate_chairperson_priority' => $this->{"indian_examiner_{$i}_senate_chairperson_priority"},
                'profile' => $profile,
            ]);
        }

        return $items;
    }

    /**
     * Get 4 International examiner objects with combined profile and form workflow data.
     */
    public function getInternationalExaminers()
    {
        $profiles = $this->examiners->where('type', 'international')->keyBy('email');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $email = $this->{"international_examiner_{$i}_email"};
            if (!$email) {
                continue;
            }
            $profile = $profiles->get($email);
            $items->push((object)[
                'slot' => $i,
                'id' => $profile?->id ?? $i,
                'type' => 'international',
                'name' => $profile?->name ?? '',
                'designation' => $profile?->designation ?? '',
                'organization' => $profile?->organization ?? '',
                'postal_address' => $profile?->postal_address ?? '',
                'email' => $email,
                'phone_number' => $profile?->phone_number ?? null,
                'phone_country_code' => $profile?->phone_country_code ?? '+1',
                'phone_iso2' => $profile?->phone_iso2 ?? 'us',
                'website' => $profile?->website ?? null,
                'research_area' => $profile?->research_area ?? null,
                'has_consent' => (bool)$this->{"international_examiner_{$i}_has_consent"},
                'consent_doc_path' => $this->{"international_examiner_{$i}_consent_doc_path"},
                'academic_office_remark' => $this->{"international_examiner_{$i}_academic_office_remark"},
                'doaa_remark' => $this->{"international_examiner_{$i}_doaa_remark"},
                'doaa_priority' => $this->{"international_examiner_{$i}_doaa_priority"},
                'senate_chairperson_priority' => $this->{"international_examiner_{$i}_senate_chairperson_priority"},
                'profile' => $profile,
            ]);
        }

        return $items;
    }

    /**
     * Get 4 OEB Chairperson objects with combined profile and form workflow data.
     */
    public function getOebChairpersons()
    {
        $profiles = $this->oebChairpersons->keyBy('email');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $email = $this->{"oeb_chairperson_{$i}_email"};
            if (!$email) {
                continue;
            }
            $profile = $profiles->get($email);
            $items->push((object)[
                'slot' => $i,
                'id' => $profile?->id ?? $i,
                'name' => $profile?->name ?? '',
                'designation' => $profile?->designation ?? '',
                'department' => $profile?->department ?? '',
                'email' => $email,
                'academic_office_remark' => $this->{"oeb_chairperson_{$i}_academic_office_remark"},
                'doaa_remark' => $this->{"oeb_chairperson_{$i}_doaa_remark"},
                'doaa_priority' => $this->{"oeb_chairperson_{$i}_doaa_priority"},
                'senate_chairperson_priority' => $this->{"oeb_chairperson_{$i}_senate_chairperson_priority"},
                'profile' => $profile,
            ]);
        }

        return $items;
    }

    /**
     * Get all co-supervisors assigned to this form keyed by slot index (1 to 10).
     * Executes in 1 single database query.
     */
    public function getCoSupervisors(): array
    {
        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($id = $this->{"co_supervisor_{$i}_id"}) {
                $ids[$i] = $id;
            }
        }

        if (empty($ids)) {
            return [];
        }

        $users = User::with('externalSupervisorProfile')->whereIn('id', array_values($ids))->get()->keyBy('id');

        $result = [];
        foreach ($ids as $slot => $userId) {
            if (isset($users[$userId])) {
                $result[$slot] = $users[$userId];
            }
        }

        return $result;
    }

    // Numerical rank for role in PTS-3 workflow hierarchy.
    public static function getRoleRank(?string $role): int
    {
        if (!$role) {
            return 999;
        }

        return match ($role) {
            'main_supervisor' => 1,
            'co_supervisors', 'co_supervisor', 'external_supervisor' => 2,
            'dpgc' => 3,
            'hod' => 4,
            'academic_office' => 5,
            'doaa', 'adoaa' => 6,
            'senate_chairperson' => 7,
            default => 999,
        };
    }

    /**
     * Check if the form has passed or submitted a specific evaluation stage.
     */
    public function hasPassedStage(string $stage): bool
    {
        $currentRank = self::getRoleRank($this->current_stage);
        $targetRank = self::getRoleRank($stage);

        if ($currentRank > $targetRank) {
            return true;
        }

        return match ($stage) {
            'main_supervisor'    => !is_null($this->main_supervisor_submitted_at) && !is_null($this->main_supervisor_recommendation),
            'co_supervisors'     => !is_null($this->co_supervisors_submitted_at),
            'dpgc'               => !is_null($this->dpgc_submitted_at) && !is_null($this->dpgc_recommendation),
            'hod'                => !is_null($this->hod_submitted_at) && !is_null($this->hod_recommendation),
            'academic_office'    => !is_null($this->academic_office_submitted_at) && !is_null($this->academic_office_is_verified),
            'doaa'               => !is_null($this->doaa_submitted_at) && !is_null($this->doaa_is_verified),
            'senate_chairperson' => !is_null($this->senate_chairperson_submitted_at) && !is_null($this->senate_chairperson_approval),
            default              => false,
        };
    }

    public function canUserViewRevertedForm(?User $user): bool
    {
        if (!$user || $this->status !== 'reverted') {
            return false;
        }

        if ($user->isStudent()) {
            return false;
        }

        $reverterRole = $this->reverted_by_role;
        if (!$reverterRole) {
            return false;
        }

        $reverterRank = self::getRoleRank($reverterRole);

        if ($user->isFaculty()) {
            $isMain = ($this->main_supervisor_id === $user->id);
            $isCo = false;
            for ($i = 1; $i <= 10; $i++) {
                if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                    $isCo = true;
                    break;
                }
            }

            if ($isMain && $reverterRank >= 1) return true;
            if ($isCo && $reverterRank >= 2) return true;
            if ($user->isDpgc() && $reverterRank >= 3) return true;
            if ($user->isHod() && $reverterRank >= 4) return true;
        }

        if ($user->isExternalSupervisor()) {
            $isCo = false;
            for ($i = 1; $i <= 10; $i++) {
                if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                    $isCo = true;
                    break;
                }
            }
            if ($isCo && $reverterRank >= 2) return true;
        }

        if ($user->isAcademicOffice() && $reverterRank >= 5) return true;
        if (($user->isDoaa() || $user->isAdoaa()) && $reverterRank >= 6) return true;
        if ($user->isSenateChairperson() && $reverterRank >= 7) return true;

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email) && $reverterRank >= 6) {
            return true;
        }

        return false;
    }

    // Determine access status for in-progress PTS-3 submission: 'allowed', 'pending_endorsement', 'not_reached', or 'unauthorized'
    public function getUserSubmissionAccessStatus(?User $user): string
    {
        if (!$user || $user->isStudent()) {
            return 'unauthorized';
        }

        $thesis = $this->thesis;
        $student = $thesis?->student;
        $stageRank = self::getRoleRank($this->current_stage);

        // 1. Main Supervisor (Rank 1)
        if ($student?->isMainSupervisor($user) || $this->main_supervisor_id === $user->id) {
            if ($stageRank < 1) return 'not_reached';
            if ($stageRank === 1) {
                return ($this->main_supervisor_submitted_at && $this->main_supervisor_recommendation !== null) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 2. Co-Supervisors (Internal and External) (Rank 2)
        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"co_supervisor_{$i}_id"} == $user->id) {
                if ($stageRank < 2) return 'not_reached';
                if ($stageRank === 2) {
                    $subCol = "co_supervisor_{$i}_submitted_at";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    return ($this->$subCol && !is_null($this->$recCol)) ? 'allowed' : 'pending_endorsement';
                }
                return 'allowed';
            }
        }

        // 3. DPGC (Rank 3)
        if ($user->isDpgc() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 3) return 'not_reached';
            if ($stageRank === 3) {
                return ($this->dpgc_submitted_at && !is_null($this->dpgc_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 4. HOD (Rank 4)
        if ($user->isHod() && ($user->deptAuthorityProfile?->department_id === $student?->department_id || !$user->deptAuthorityProfile)) {
            if ($stageRank < 4) return 'not_reached';
            if ($stageRank === 4) {
                return ($this->hod_submitted_at && !is_null($this->hod_recommendation)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 5. Academic Office (Rank 5)
        if ($user->isAcademicOffice()) {
            if ($stageRank < 5) return 'not_reached';
            if ($stageRank === 5) {
                return ($this->academic_office_submitted_at && !is_null($this->academic_office_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 6. DOAA / ADoAA / Acting DOAA / Vested DOAA (Rank 6)
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email))) {
            if ($stageRank < 6) return 'not_reached';
            if ($stageRank === 6) {
                return ($this->doaa_submitted_at && !is_null($this->doaa_is_verified)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        // 7. Senate Chairperson (Rank 7)
        if ($user->isSenateChairperson()) {
            if ($stageRank < 7) return 'not_reached';
            if ($stageRank === 7) {
                return ($this->senate_chairperson_submitted_at && !is_null($this->senate_chairperson_approval)) ? 'allowed' : 'pending_endorsement';
            }
            return 'allowed';
        }

        return 'unauthorized';
    }

    // Check if user is authorized to view this PTS-3 form in its current state
    public function canUserView(?User $user): bool
    {
        if (!$user || $user->isStudent()) {
            return false;
        }

        if ($this->status === 'reverted') {
            return $this->canUserViewRevertedForm($user);
        }

        if ($this->status === 'in_progress') {
            $access = $this->getUserSubmissionAccessStatus($user);
            return $access === 'allowed' || $access === 'pending_endorsement';
        }

        // For completed forms (approved / rejected)
        $thesis = $this->thesis;
        $student = $thesis?->student;

        if ($student && ($student->isSupervisor($user) || $this->main_supervisor_id === $user->id)) {
            return true;
        }

        for ($i = 1; $i <= 10; $i++) {
            if ($this->{"co_supervisor_{$i}_id"} === $user->id) {
                return true;
            }
        }

        if ($user->isDpgc() || $user->isHod()) {
            $userDeptId = $user->deptAuthorityProfile?->department_id ?? $user->facultyProfile?->department_id;
            if ($userDeptId && $student && $student->department_id === $userDeptId) {
                return true;
            }
        }

        if ($user->isAcademicOffice() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson()) {
            return true;
        }

        if ($user->isActingApprovalAuthority() && ($this->acting_doaa_email === $user->email || $this->vested_doaa_email === $user->email)) {
            return true;
        }

        return false;
    }

    // Check if user is currently authorized to review/endorse this PTS-3 form
    public function canUserReview(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        return $this->getUserSubmissionAccessStatus($user) === 'pending_endorsement';
    }

    // Check if user is authorized to revert this form back to Main Supervisor
    public function canUserRevert(?User $user): bool
    {
        if (!$user || $this->status !== 'in_progress') {
            return false;
        }

        $stageRank = self::getRoleRank($this->current_stage);
        if ($stageRank < 3) {
            return false;
        }

        return $this->canUserReview($user);
    }

    public function getRevertedByRoleLabel(): string
    {
        return Thesis::getRevertedByRoleLabel($this);
    }

    // Get the reversion comment left by the reverting authority.
    public function getReversionComment(): ?string
    {
        return $this->reversion_comment;
    }

    // Accessor for human-readable stage label mapped from Thesis.
    public function getStageLabelAttribute(): string
    {
        return Thesis::getStageLabel($this->current_stage);
    }

    // Usage in Blade: {{ $pts3Form->status_label }}
    public function getStatusLabelAttribute(): string
    {
        return Thesis::getStatusLabel($this->status);
    }

    /**
     * Get submitted timeline steps for submission-timeline-modal component.
     */
    public function getSubmittedTimeline(): array
    {
        return Thesis::getSubmissionTimeline($this);
    }

    public function revertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by_id');
    }

    public function mainSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_supervisor_id');
    }

    public function dpgcUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dpgc_user_id');
    }

    public function hodUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_user_id');
    }

    public function academicOfficeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_office_user_id');
    }

    public function doaaUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doaa_user_id');
    }

    public function senateChairpersonUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'senate_chairperson_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
