<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class GenerateTokenRequest extends FormRequest
{
    public function __construct(
        public CheckCredentialsRequest $checkCredentialsRequest
    ) {
        $this->checkCredentialsRequest = resolve(CheckCredentialsRequest::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Exists|string>| ValidationRule|string>
     */
    public function rules(): array
    {
        return [...$this->checkCredentialsRequest->rules(), 'company_id' => ['required', Rule::exists('companies', 'id')]];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return $this->checkCredentialsRequest->after();
    }

    /**
     * @param  array<int, string>|int|string|null  $key
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        return array_merge($this->checkCredentialsRequest->validated(), parent::validated());
    }
}
