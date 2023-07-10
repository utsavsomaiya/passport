<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateTokenRequest extends FormRequest
{
    public function __construct(
        public UserRequest $userRequest
    ) {
        $this->userRequest = resolve(UserRequest::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge($this->userRequest->rules(), [
            'company_id' => ['required', Rule::exists('companies', 'id')],
        ]);
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return $this->userRequest->after();
    }

    public function validated($key = null, $default = null)
    {
        return array_merge($this->userRequest->validated(), parent::validated());
    }
}
