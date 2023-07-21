<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | Unique> | string)>
     */
    public function rules(): array
    {
        $currencyId = null;

        if ('api.currencies.update' === $this->route()?->getName()) {
            $currencyId = $this->route()->parameter('id');
        }

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('currencies')->ignore($currencyId)->where('company_id', app('company_id')),
            ],
            'format' => ['required', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ];
    }
}
