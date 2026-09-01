<?php

namespace App\Http\Requests\Pts3;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePts3Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts3 = $this->route('pts3');
        $thesis = $pts3?->thesis;
        return $thesis && $thesis->student && $thesis->student->isMainSupervisor($this->user());
    }

    public function rules(): array
    {
        return [
            'thesis_title' => 'required|string|max:1000',

            // Indian Examiners (Minimum 2, Maximum 4)
            'indian_examiners' => 'required|array|min:2|max:4',
            'indian_examiners.*.name' => 'required|string',
            'indian_examiners.*.designation' => 'required|string',
            'indian_examiners.*.organization' => 'required|string',
            'indian_examiners.*.postal_address' => 'required|string',
            'indian_examiners.*.email' => 'required|email',
            'indian_examiners.*.phone_number' => 'required|string',
            'indian_examiners.*.phone_country_code' => 'required|string|max:5',
            'indian_examiners.*.phone_iso2' => 'required|string|max:10',
            'indian_examiners.*.website' => 'nullable|string|url',
            'indian_examiners.*.research_area' => 'nullable|string',
            'indian_examiners.*.has_consent' => 'required|boolean',
            'indian_examiners.*.consent_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            // International Examiners (Minimum 2, Maximum 4)
            'international_examiners' => 'required|array|min:2|max:4',
            'international_examiners.*.name' => 'required|string',
            'international_examiners.*.designation' => 'required|string',
            'international_examiners.*.organization' => 'required|string',
            'international_examiners.*.postal_address' => 'required|string',
            'international_examiners.*.email' => 'required|email',
            'international_examiners.*.phone_number' => 'required|string',
            'international_examiners.*.phone_country_code' => 'required|string|max:5',
            'international_examiners.*.phone_iso2' => 'required|string|max:10',
            'international_examiners.*.website' => 'nullable|string|url',
            'international_examiners.*.research_area' => 'nullable|string',
            'international_examiners.*.has_consent' => 'required|boolean',
            'international_examiners.*.consent_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',

            // OEB Members (Exactly 4)
            'oeb_members' => 'required|array|size:4',
            'oeb_members.*.name' => 'required|string',
            'oeb_members.*.designation' => 'required|string',
            'oeb_members.*.department' => 'required|string',
            'oeb_members.*.email' => 'required|email',
            'oeb_members.*.phone_number' => 'nullable|string',
            'oeb_members.*.phone_country_code' => 'nullable|string|max:5',
            'oeb_members.*.phone_iso2' => 'nullable|string|max:10',
        ];
    }
}
