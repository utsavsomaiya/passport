<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FetchProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array', 'max:7'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.sku' => ['sometimes', 'string', 'max:255'],
            'filter.upc_ean' => ['sometimes', 'string', 'max:255'],
            'filter.is_bundle' => ['sometimes', 'boolean'],
            'filter.status' => ['sometimes', 'boolean'],
            'filter.has_hierarchies' => ['sometimes', 'boolean'],
            'filter.hierarchy_id' => ['sometimes', 'string', 'uuid'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
