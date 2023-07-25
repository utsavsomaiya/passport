<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\FieldType;
use App\Models\Attribute;
use App\Models\Template;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;

class AttributeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (array<int, (Exists | RequiredIf | string | Unique)> | null)>
     */
    public function rules(): array
    {
        $attributeId = null;

        if ('api.attributes.update' === $this->route()?->getName()) {
            $attributeId = $this->route()->parameter('id');
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Attribute::class)->ignore($attributeId)->where('template_id', $this->template_id)],
            'template_id' => ['required', Rule::exists(Template::class, 'id')->where('company_id', app('company_id'))],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'field_type' => ['required', 'string', 'in:' . FieldType::getValidationNames()],
            'options' => ['sometimes', Rule::requiredIf(fn (): bool => in_array($this->field_type, FieldType::selections())), 'array'],
            'options.*' => ['required_with:options', 'string', 'max:255'],
            'validation' => ['sometimes', 'array', 'max:2'],
            'validation.from' => ['required_with:validation', ...$this->basedOnFieldTypeExtraValidation()],
            'validation.to' => ['required_with:validation', ...$this->basedOnFieldTypeExtraValidation()],
            'order' => ['sometimes', 'integer'],
            'default_value' => $this->defaultValueValidation(),
            'is_required' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
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

    /**
     * @return array<int, string>
     */
    private function defaultValueValidation(): array
    {
        $validation = ['sometimes'];

        if (($fieldType = FieldType::tryFromName(Str::upper($this->field_type))) instanceof FieldType) {
            $fieldTypeValidation = $fieldType->validation($this->get('from'), $this->get('to'));

            if (in_array($this->field_type, FieldType::selections())) {
                $fieldTypeValidation[] = 'in:' . $this->options;
            }

            $validation = [...$validation, ...$fieldTypeValidation];
        }

        return $validation;
    }

    /**
     * @param  array<int, string>|int|string|null  $key
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        return array_merge(parent::validated(), [
            'field_type' => FieldType::tryFromName(Str::upper($this->field_type))?->value,
        ]);
    }
}
