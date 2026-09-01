<?php

namespace App\Http\Requests\Pts2;

use Illuminate\Foundation\Http\FormRequest;

class EndorsePts2Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts2 = $this->route('pts2');
        return $pts2 && $this->user()->can('evaluate', $pts2);
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

        if ($stage === 'main_supervisor') {
            return [
                'cert_prima_facie_case' => 'required|boolean',
                'cert_no_prior_degree_submission' => 'required|boolean',
                'collaborative_work_status' => 'required|boolean',
                'collaborative_work_details' => $this->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
                'recommendation' => 'required|boolean',
                'student_comment' => 'nullable|string|max:2000',
                'confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
            ];
        }

        return [
            'recommendation' => 'required|boolean',
            'student_comment' => 'nullable|string|max:2000',
            'confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ];
    }
}
