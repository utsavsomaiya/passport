<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | Unique> | string)>
     */
    public function rules(): array
    {
        $roleId = null;
        if ($this->route()?->getName() === 'api.roles.update') {
            $roleId = $this->route()->parameter('id');
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($roleId)],
            'description' => ['nullable', 'string'],
        ];
    }
}
