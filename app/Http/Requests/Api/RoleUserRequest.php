<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'user' => ['required', Rule::exists(User::class, 'id')],
            'roles' => ['required', 'array', Rule::exists(Role::class, 'id')->where('company_id', app('company_id'))],
            'roles.*' => ['required', 'uuid', 'string'],
        ];
    }
}
