<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\FieldType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\RequiredIf;

class AttributeRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'field_type' => (int) $this->field_type,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (array<int, (Exists | RequiredIf | string)> | null)>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'template_id' => ['required', Rule::exists('templates', 'id')->where('company_id', app('company_id'))],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'field_type' => ['required', 'in:'.FieldType::getValidationValues()],
            'options' => ['sometimes', Rule::requiredIf(fn (): bool => in_array($this->field_type, FieldType::selections())), 'array'],
            'from' => ['nullable', 'numeric'],
            'to' => ['nullable', 'numeric'],
            'order' => ['nullable', 'integer'],
            'default_value' => FieldType::tryFrom($this->field_type)?->validation($this->get('from'), $this->get('to')),
            'is_required' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @param  array<int, string>|int|string|null  $key
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated(), [
            'validation' => FieldType::tryFrom($this->field_type)?->validation(),
        ]);
    }
}
