<?php

namespace App\Http\Requests\Pts4;

use Illuminate\Foundation\Http\FormRequest;

class RevertPts4Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts4 = $this->route('pts4');
        return $pts4 && $this->user()->can('revert', $pts4);
    }

    public function rules(): array
    {
        return [
            'reversion_comment' => 'required|string|max:2000',
            'revert_to' => 'nullable|string',
        ];
    }
}
