<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pts3Draft extends Model
{
    use HasFactory;

    protected $table = 'pts3_drafts';

    protected $guarded = [];

    protected $casts = [
        'indian_examiner_1_has_consent' => 'boolean',
        'indian_examiner_2_has_consent' => 'boolean',
        'indian_examiner_3_has_consent' => 'boolean',
        'indian_examiner_4_has_consent' => 'boolean',
        'international_examiner_1_has_consent' => 'boolean',
        'international_examiner_2_has_consent' => 'boolean',
        'international_examiner_3_has_consent' => 'boolean',
        'international_examiner_4_has_consent' => 'boolean',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examinerDrafts(): HasMany
    {
        return $this->hasMany(Pts3ExaminerDraft::class, 'pts3_draft_id');
    }

    public function oebChairpersonDrafts(): HasMany
    {
        return $this->hasMany(Pts3OebChairpersonDraft::class, 'pts3_draft_id');
    }

    /**
     * Get 1..4 Indian examiner draft objects combining slot and profile details.
     */
    public function getIndianExaminers()
    {
        $profiles = $this->examinerDrafts->where('type', 'indian')->keyBy('slot');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $slotEmail = $this->{"indian_examiner_{$i}_email"};
            $profile = $profiles->get($i) ?? (!empty($slotEmail) ? $this->examinerDrafts->where('type', 'indian')->where('email', $slotEmail)->first() : null);
            $email = $slotEmail ?? ($profile?->email ?? null);
            $docPath = $this->{"indian_examiner_{$i}_consent_doc_path"};

            if ($profile || !empty($email) || !empty($docPath)) {
                $items->push((object)[
                    'slot' => $i,
                    'id' => $profile?->id ?? $i,
                    'type' => 'indian',
                    'name' => $profile?->name ?? '',
                    'designation' => $profile?->designation ?? '',
                    'organization' => $profile?->organization ?? '',
                    'postal_address' => $profile?->postal_address ?? '',
                    'email' => $email ?? '',
                    'phone_number' => $profile?->phone_number ?? '',
                    'phone_country_code' => $profile?->phone_country_code ?? '+91',
                    'phone_iso2' => $profile?->phone_iso2 ?? 'in',
                    'website' => $profile?->website ?? '',
                    'research_area' => $profile?->research_area ?? '',
                    'has_consent' => (bool)$this->{"indian_examiner_{$i}_has_consent"},
                    'consent_doc_path' => $docPath,
                    'profile' => $profile,
                ]);
            }
        }

        return $items;
    }

    /**
     * Get 1..4 International examiner draft objects combining slot and profile details.
     */
    public function getInternationalExaminers()
    {
        $profiles = $this->examinerDrafts->where('type', 'international')->keyBy('slot');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $slotEmail = $this->{"international_examiner_{$i}_email"};
            $profile = $profiles->get($i) ?? (!empty($slotEmail) ? $this->examinerDrafts->where('type', 'international')->where('email', $slotEmail)->first() : null);
            $email = $slotEmail ?? ($profile?->email ?? null);
            $docPath = $this->{"international_examiner_{$i}_consent_doc_path"};

            if ($profile || !empty($email) || !empty($docPath)) {
                $items->push((object)[
                    'slot' => $i,
                    'id' => $profile?->id ?? $i,
                    'type' => 'international',
                    'name' => $profile?->name ?? '',
                    'designation' => $profile?->designation ?? '',
                    'organization' => $profile?->organization ?? '',
                    'postal_address' => $profile?->postal_address ?? '',
                    'email' => $email ?? '',
                    'phone_number' => $profile?->phone_number ?? '',
                    'phone_country_code' => $profile?->phone_country_code ?? '+1',
                    'phone_iso2' => $profile?->phone_iso2 ?? 'us',
                    'website' => $profile?->website ?? '',
                    'research_area' => $profile?->research_area ?? '',
                    'has_consent' => (bool)$this->{"international_examiner_{$i}_has_consent"},
                    'consent_doc_path' => $docPath,
                    'profile' => $profile,
                ]);
            }
        }

        return $items;
    }

    /**
     * Get 1..4 OEB Chairperson draft objects combining slot and profile details.
     */
    public function getOebChairpersons()
    {
        $profiles = $this->oebChairpersonDrafts->keyBy('slot');
        $items = collect();

        for ($i = 1; $i <= 4; $i++) {
            $slotEmail = $this->{"oeb_chairperson_{$i}_email"};
            $profile = $profiles->get($i) ?? (!empty($slotEmail) ? $this->oebChairpersonDrafts->where('email', $slotEmail)->first() : null);
            $items->push((object)[
                'slot' => $i,
                'id' => $profile?->id ?? $i,
                'name' => $profile?->name ?? '',
                'designation' => $profile?->designation ?? '',
                'department' => $profile?->department ?? '',
                'email' => $slotEmail ?? ($profile?->email ?? ''),
                'profile' => $profile,
            ]);
        }

        return $items;
    }
}
