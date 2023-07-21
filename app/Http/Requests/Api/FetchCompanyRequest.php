<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FetchCompanyRequest extends CheckCredentialsRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'filter' => ['sometimes', 'array', 'max:2'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.email' => ['sometimes', 'string', 'email'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
