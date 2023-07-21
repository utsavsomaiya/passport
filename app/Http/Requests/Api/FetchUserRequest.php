<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FetchUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array', 'max:4'],
            'filter.first_name' => ['sometimes', 'string', 'max:255'],
            'filter.last_name' => ['sometimes', 'string', 'max:255'],
            'filter.username' => ['sometimes', 'string', 'max:255'],
            'filter.email' => ['sometimes', 'email', 'max:255'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
