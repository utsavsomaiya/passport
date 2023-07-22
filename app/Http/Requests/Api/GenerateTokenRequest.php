<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class GenerateTokenRequest extends CheckCredentialsRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Exists|string>| ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'company_id' => ['required', Rule::exists('companies', 'id')],
        ];
    }
}
