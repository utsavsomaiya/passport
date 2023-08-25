<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FetchHierarchyProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array', 'max:1'],
            'filter.is_curated_product' => ['sometimes', 'boolean'],
        ];
    }
}
