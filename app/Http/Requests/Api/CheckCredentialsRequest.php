<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\User;
use App\Queries\UserQueries;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class CheckCredentialsRequest extends FormRequest
{
    protected ?User $user = null;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
            'filter' => ['sometimes', 'array', 'max:2'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.email' => ['sometimes', 'string', 'email'],
            'sort' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        $this->user = resolve(UserQueries::class)->findByEmail($this->email);

        return [
            function (Validator $validator): void {
                if (! $this->user || ! Hash::check($this->password, $this->user->password)) {
                    $validator->errors()->add('email', 'The provided credentials are incorrect');
                }
            },
        ];
    }

    /**
     * @param  array<int, string>|int|string|null  $key
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated(), [
            'user' => $this->user,
        ]);
    }
}
