<?php

namespace App\Http\Requests\Pts2;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePts2SupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pts2 = $this->route('pts2');
        return $pts2 && $this->user()->can('mainSupervisorEdit', $pts2);
    }

    public function rules(): array
    {
        return [
            'thesis_title' => 'required|string|max:1000',
            'cert_prima_facie_case' => 'required|boolean',
            'cert_no_prior_degree_submission' => 'required|boolean',
            'collaborative_work_status' => 'required|boolean',
            'collaborative_work_details' => $this->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
            'synopsis_report_doc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'recommendation' => 'required|boolean',
            'main_supervisor_student_comment' => 'nullable|string|max:2000',
            'main_supervisor_confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ];
    }
}
