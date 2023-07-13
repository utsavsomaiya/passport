<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\FieldType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class AttributeRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = null;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<int, string|RequiredIf>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'template_id' => ['required', Rule::exists('templates', 'id')->where('company_id', app('company_id'))],
            'description' => ['nullable'],
            'slug' => ['sometimes', 'string'],
            'field_type' => ['required', 'in:'.FieldType::getValidationValues()],
            'options' => [Rule::requiredIf(fn () => in_array($this->field_type, FieldType::selections())), 'array'],
            'from' => [],
            'to' => [],
            'default_value' => FieldType::tryFrom($this->field_type)->validation(),
            'is_required' => ['boolean'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        return array_merge(parent::validated(), [
            'validation' => FieldType::tryFrom($this->field_type)->validation()
        ]);
    }
}
