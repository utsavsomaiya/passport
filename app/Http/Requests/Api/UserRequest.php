<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string|Password|Unique|null> | string)>
     */
    public function rules(): array
    {
        $userId = null;

        if ($this->route()?->getName() === 'api.users.update') {
            $userId = $this->route()->parameter('id');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($userId)],
            'email' => ['required', 'string', 'email', Rule::unique(User::class)->ignore($userId)],
        ];

        if ($this->route()?->getName() === 'api.users.create') {
            $rules['password'] = ['required', 'string', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    /**
     * @param  array<int, string>|int|string|null  $key
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        if ($this->route()?->getName() === 'api.users.create') {
            return array_merge(parent::validated(), [
                'password' => bcrypt($this->password),
            ]);
        }

        return parent::validated();
    }
}
