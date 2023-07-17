<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\FieldType;
use Illuminate\Foundation\Http\FormRequest;
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
        $fromToValidation = ['nullable', ...$this->basedOnFieldTypeExtraValidation()];

        return [
            'name' => ['required', 'string', 'max:255'],
            'template_id' => ['required', Rule::exists('templates', 'id')->where('company_id', app('company_id'))],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'field_type' => ['required', 'in:'.FieldType::getValidationValues()],
            'options' => ['sometimes', Rule::requiredIf(fn (): bool => in_array($this->field_type, FieldType::selections())), 'array'],
            'options.*' => ['sometimes', 'string', 'max:255'],
            'from' => $fromToValidation,
            'to' => $fromToValidation,
            'order' => ['nullable', 'integer'],
            'default_value' => array_merge(['nullable'], FieldType::tryFrom($this->field_type)?->validation($this->get('from'), $this->get('to'))),
            'is_required' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function basedOnFieldTypeExtraValidation(): array
    {
        $validation = [];

        if ($this->field_type === FieldType::NUMBER->value || $this->field_type === FieldType::DECIMAL->value) {
            $validation[] = 'numeric';
        }

        if ($this->field_type === FieldType::DATE->value || $this->field_type === FieldType::DATETIME->value) {
            $validation[] = 'date';
        }

        return $validation;
    }
}
