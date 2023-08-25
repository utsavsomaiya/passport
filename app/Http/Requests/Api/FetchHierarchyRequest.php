<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FetchHierarchyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string> | string)>
     */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array', 'max:3'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.id' => ['sometimes', 'string', 'uuid'],
            'filter.product_id' => ['sometimes', 'string', 'uuid'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
