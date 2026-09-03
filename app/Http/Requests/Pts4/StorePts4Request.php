<?php

namespace App\Http\Requests\Pts4;

use Illuminate\Foundation\Http\FormRequest;

class StorePts4Request extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStudent();
    }

    public function rules(): array
    {
        $thesis = $this->user()->student?->activeThesis;
        $isReverted = $thesis && $thesis->pts4Form && $thesis->pts4Form->status === 'reverted';
        $hasDoc = $thesis && $thesis->pts4Form && $thesis->pts4Form->thesis_doc_path;

        return [
            'thesis_title' => 'required|string|max:1000',
            'thesis_doc' => ($isReverted && $hasDoc)
                ? 'nullable|file|mimes:pdf,doc,docx|max:102400'
                : 'required|file|mimes:pdf,doc,docx|max:102400',
            'hindi_name' => 'nullable|string|max:255',
            'alternate_email' => 'nullable|email|max:255',
            'alternate_phone_number' => 'nullable|string|max:20',
            'alternate_phone_country_code' => 'nullable|string|max:10',
            'alternate_phone_iso2' => 'nullable|string|max:5',
            'declaration_norms' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'thesis_title.required' => 'Please provide the title of the thesis.',
            'thesis_doc.required' => 'Please upload the thesis document (PDF or Word up to 100MB).',
            'thesis_doc.max' => 'The thesis document must not exceed 100MB.',
            'declaration_norms.accepted' => 'You must certify that all copies of the thesis have been prepared strictly in accordance with IIT Indore norms.',
        ];
    }
}
