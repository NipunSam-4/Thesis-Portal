<?php

namespace App\Http\Requests\Pts2;

use App\Models\Thesis;
use Illuminate\Foundation\Http\FormRequest;

class StorePts2Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->student !== null;
    }

    public function rules(): array
    {
        $student = auth()->user()->student;
        $thesis = $student ? Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts2Form'])->first() : null;
        $pts2Form = $thesis?->pts2Form;
        $hasExisting = $pts2Form && in_array($pts2Form->status, ['reverted', 'rejected']);
        $fileRequired = $hasExisting && $pts2Form->synopsis_report_doc_path ? 'nullable' : 'required';

        return [
            'thesis_title' => 'required|string|max:1000',
            'current_address' => 'required|string|max:1000',
            'alternate_email' => 'nullable|email|max:255',
            'recent_phone_country_code' => 'required|string|max:5',
            'recent_phone_number' => 'required|digits_between:5,15',
            'recent_phone_iso2' => 'required|string|max:10',
            'alternate_phone_country_code' => 'nullable|string|max:5',
            'alternate_phone_number' => 'nullable|digits_between:5,15',
            'alternate_phone_iso2' => 'nullable|string|max:10',
            'course_credits_student' => 'required|numeric|min:0',
            'cert_prima_facie_case' => 'required|accepted',
            'cert_no_prior_degree_submission' => 'required|accepted',
            'collaborative_work_status' => 'required|boolean',
            'collaborative_work_details' => $this->boolean('collaborative_work_status') ? 'required|string|max:2000' : 'nullable|string',
            'student_declaration' => 'required|accepted',
            'synopsis_report_doc' => "{$fileRequired}|file|mimes:pdf,doc,docx|max:10240",
        ];
    }
}
