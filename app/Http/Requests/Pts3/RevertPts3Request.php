<?php

namespace App\Http\Requests\Pts3;

use Illuminate\Foundation\Http\FormRequest;

class RevertPts3Request extends FormRequest
{
    public function authorize(): bool
    {
        $pts3 = $this->route('pts3');
        return $pts3 && $this->user()->can('revert', $pts3);
    }

    public function rules(): array
    {
        return [
            'reversion_comment' => 'required|string|max:2000',
        ];
    }
}
