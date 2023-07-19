<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            'user' => ['required', 'exists:users,id'],
            'roles' => ['required', 'array', 'exists:roles,id'],
            'roles.*' => ['required', 'uuid', 'string'],
        ];
    }
}
