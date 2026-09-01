<?php

namespace App\Http\Requests\Pts2;

use Illuminate\Foundation\Http\FormRequest;

class RevertPts2Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts2 = $this->route('pts2');
        return $pts2 && $this->user()->can('revert', $pts2);
    }

    public function rules(): array
    {
        return [
            'reversion_comment' => 'required|string|max:2000',
            'revert_to' => 'nullable|string',
        ];
    }
}
