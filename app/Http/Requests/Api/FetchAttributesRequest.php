<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FetchAttributesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            'templateId' => ['sometimes', 'string', 'uuid', 'exists:templates,id'],
            'filter' => ['sometimes', 'array', 'max:3'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.template_name' => ['sometimes', 'string', 'max:255'],
            'filter.options' => ['sometimes', 'array'],
            'filter.options.*' => ['required_with:filter.options', 'string', 'max:255'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
