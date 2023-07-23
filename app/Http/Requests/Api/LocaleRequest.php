<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Locale;
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

        if ('api.locales.update' === $this->route()?->getName()) {
            $localeId = $this->route()->parameter('id');
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Locale::class)->ignore($localeId)->where('company_id', app('company_id'))],
            'code' => ['required', 'string', 'max:255', Rule::unique(Locale::class)->ignore($localeId)->where('company_id', app('company_id'))],
            'status' => ['required', 'boolean'],
        ];
    }
}
