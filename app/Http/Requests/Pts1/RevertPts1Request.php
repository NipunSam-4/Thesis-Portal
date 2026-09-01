<?php

namespace App\Http\Requests\Pts1;

use Illuminate\Foundation\Http\FormRequest;

class RevertPts1Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts1 = $this->route('pts1');
        return $pts1 && $this->user()->can('revert', $pts1);
    }

    public function rules(): array
    {
        return [
            'reversion_comment' => 'required|string|max:2000',
            'revert_to' => 'nullable|string',
        ];
    }
}
