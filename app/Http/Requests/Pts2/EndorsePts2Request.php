<?php

namespace App\Http\Requests\Pts2;

use Illuminate\Foundation\Http\FormRequest;

class EndorsePts2Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts2 = $this->route('pts2');
        return $pts2 && $this->user()->can('review', $pts2);
    }

    public function rules(): array
    {
        $pts2 = $this->route('pts2');
        $stage = $pts2 ? $pts2->current_stage : null;

        if ($stage === 'academic_office') {
            return [
                'verified_details' => 'required|accepted',
                'verification_remark' => 'required|string|max:2000',
                'academic_office_course_credits' => 'required|numeric|min:0',
                'acting_doaa_email' => 'nullable|email',
            ];
        }

        return [
            'recommendation' => 'required|boolean',
            'student_comment' => 'nullable|string|max:2000',
            'confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ];
    }
}
