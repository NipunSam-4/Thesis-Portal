<?php

namespace App\Http\Requests\Pts1;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePts1SupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pts1 = $this->route('pts1');
        return $pts1 && $this->user()->can('supervisorEdit', $pts1);
    }

    public function rules(): array
    {
        $pts1 = $this->route('pts1');
        $pubNormFulfilled = $this->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? null : ($this->has('special_approval_publication') ? $this->boolean('special_approval_publication') : null);

        $minTimeFulfilled = $this->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? null : ($this->has('special_approval_min_time') ? $this->boolean('special_approval_min_time') : null);

        $requirePubDoc = !$pubNormFulfilled && $pubSpecialApproval && !$pts1->publication_approval_doc_path;
        $requireMinTimeDoc = !$minTimeFulfilled && $minTimeSpecialApproval && !$pts1->min_time_approval_doc_path;

        return [
            'thesis_title' => 'required|string|max:1000',
            'date_confirmation' => 'required|date',
            'seminar_date' => 'required|date',
            'seminar_time' => 'required|string|max:100',
            'seminar_venue' => 'required|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            
            'publication_norm_fulfillment' => 'required|boolean',
            'special_approval_publication' => 'nullable|boolean',
            'publication_approval_doc' => ($requirePubDoc ? 'required' : 'nullable') . '|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'min_time_req_fulfilled' => 'required|boolean',
            'special_approval_min_time' => 'nullable|boolean',
            'min_time_approval_doc' => ($requireMinTimeDoc ? 'required' : 'nullable') . '|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'draft_synopsis_report' => 'nullable|file|mimes:pdf,docx|max:10240',
            'publication_list' => 'nullable|file|mimes:xlsx,xls|max:2048',

            'work_status' => 'required|in:adequate,inadequate',
            'main_supervisor_student_comment' => 'required|string|max:2000',
            'main_supervisor_confidential_remark' => $this->input('work_status') === 'inadequate' ? 'required|string|max:2000' : 'nullable|string|max:2000',
            'pspc_undertaking' => 'required|accepted',
        ];
    }
}
