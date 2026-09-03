<?php

namespace App\Http\Requests\Pts4;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePts4SupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pts4 = $this->route('pts4');
        return $pts4 && $this->user()->can('mainSupervisorEdit', $pts4);
    }

    public function rules(): array
    {
        return [
            'thesis_title' => 'required|string|max:1000',
            'thesis_doc' => 'nullable|file|mimes:pdf,doc,docx|max:102400',
            'recommendation' => 'required|boolean',
            'main_supervisor_student_comment' => 'nullable|string|max:2000',
            'main_supervisor_confidential_remark' => $this->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
        ];
    }
}
