<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class PermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | In> | string)>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'uuid', 'exists:roles,id'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required_with:permissions', 'string', Rule::in(Permission::getFeatureGates()->toArray())],
        ];
    }
}
