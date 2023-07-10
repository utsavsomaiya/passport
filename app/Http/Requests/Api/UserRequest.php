<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\User;
use App\Queries\UserQueries;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    protected ?User $user;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array | string)>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        $this->user = resolve(UserQueries::class)->findByEmail($this->email);

        return [
            function (Validator $validator) {
                if (! $this->user || ! Hash::check($this->password, $this->user->password)) {
                    $validator->errors()->add('email', 'The provided credentials are incorrect');
                }
            },
        ];
    }

    public function validated($key = null, $default = null)
    {
        return array_merge(parent::validated(), [
            'user' => $this->user,
        ]);
    }
}
