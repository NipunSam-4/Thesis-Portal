<?php

namespace App\Http\Requests\Pts1;

use Illuminate\Foundation\Http\FormRequest;

class EndorsePts1Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts1 = $this->route('pts1');
        return $pts1 && $this->user()->can('evaluate', $pts1);
    }

    public function rules(): array
    {
        $pts1 = $this->route('pts1');
        $stage = $pts1 ? $pts1->current_stage : null;

        if ($stage === 'academic_office') {
            return [
                'verified_details' => 'required|accepted',
                'confidential_remark' => 'required|string|max:2000',
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
