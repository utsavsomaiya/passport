<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class LocaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string|Unique> | string)>
     */
    public function rules(): array
    {
        $localeId = null;
        $isNameRequired = 'required';
        $isCodeRequired = 'required';

        if ('api.locales.update' === $this->route()?->getName()) {
            $localeId = $this->route()->parameter('id');
            $isNameRequired = 'nullable';
            $isCodeRequired = 'nullable';
        }

        return [
            'name' => [$isNameRequired, Rule::unique('locales', 'name')->ignore($localeId)->where('company_id', app('company_id'))],
            'code' => [$isCodeRequired, Rule::unique('locales', 'code')->ignore($localeId)->where('company_id', app('company_id'))],
        ];
    }
}
