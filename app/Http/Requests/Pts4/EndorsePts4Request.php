<?php

namespace App\Http\Requests\Pts4;

use Illuminate\Foundation\Http\FormRequest;

class EndorsePts4Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts4 = $this->route('pts4');
        return $pts4 && $this->user()->can('review', $pts4);
    }

    public function rules(): array
    {
        $pts4 = $this->route('pts4');
        $stage = $pts4 ? $pts4->current_stage : null;

        if ($stage === 'academic_office') {
            return [
                'verified_details' => 'required|accepted',
                'verification_remark' => 'required|string|max:2000',
                'confidential_remark' => 'nullable|string|max:2000',
            ];
        }

        return [
            'recommendation' => 'required|boolean',
            'student_comment' => 'nullable|string|max:2000',
            'confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ];
    }
}
