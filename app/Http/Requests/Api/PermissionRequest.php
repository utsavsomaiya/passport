<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Role;
use Facades\App\Enums\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;

class PermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | In | Exists> | string)>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'uuid', Rule::exists(Role::class, 'id')->where('company_id', app('company_id'))],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'string', Rule::in(Permission::getFeatureGates()->toArray())],
        ];
    }
}
