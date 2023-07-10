<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        $isCodeRequired = 'required';
        $isFormatRequired = 'required';

        if ('api.currencies.update' === $this->route()?->getName()) {
            $isCodeRequired = 'nullable';
            $isFormatRequired = 'nullable';
        }

        return [
            'code' => [$isCodeRequired],
            'format' => [$isFormatRequired, 'string'],
            'status' => ['nullable'],
        ];
    }
}
