<?php

namespace App\Http\Requests\Pts1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePts1Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->student !== null;
    }

    public function rules(): array
    {
        $student = auth()->user()->student;
        $thesis = $student?->theses()->where('status', 'in_progress')->latest()->first();
        $pts1Form = $thesis?->pts1Form;
        $hasExisting = $pts1Form && in_array($pts1Form->status, ['reverted', 'rejected']);

        return [
            'thesis_title' => 'required|string|max:1000',
            'date_confirmation' => 'required|date',
            'seminar_date' => 'required|date',
            'seminar_time' => 'required|string|max:100',
            'seminar_venue' => 'required|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            
            'publication_norm_fulfillment' => 'required|boolean',
            'special_approval_publication' => 'nullable|boolean',
            'publication_approval_doc' => [
                'nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048',
                Rule::requiredIf(function() use ($hasExisting, $pts1Form) {
                    if ($this->boolean('publication_norm_fulfillment') || !$this->boolean('special_approval_publication')) {
                        return false;
                    }
                    return !$hasExisting || !$pts1Form->publication_approval_doc_path;
                })
            ],

            'min_time_req_fulfilled' => 'required|boolean',
            'special_approval_min_time' => 'nullable|boolean',
            'min_time_approval_doc' => [
                'nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048',
                Rule::requiredIf(function() use ($hasExisting, $pts1Form) {
                    if ($this->boolean('min_time_req_fulfilled') || !$this->boolean('special_approval_min_time')) {
                        return false;
                    }
                    return !$hasExisting || !$pts1Form->min_time_approval_doc_path;
                })
            ],

            'draft_synopsis_report' => [
                'file', 'mimes:pdf,docx', 'max:10240',
                $hasExisting && $pts1Form?->draft_synopsis_report_doc_path ? 'nullable' : 'required'
            ],
            'publication_list' => [
                'file', 'mimes:xlsx,xls', 'max:2048',
                $hasExisting && $pts1Form?->publication_list_doc_path ? 'nullable' : 'required'
            ],
        ];
    }
}
