<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        $templateId = null;
        if ($this->route()?->getName() === 'api.templates.update') {
            $templateId = $this->route()->parameter('id');
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('templates', 'name')->ignore($templateId)->where('company_id', app('company_id'))],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
