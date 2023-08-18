<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FetchBundleProductComponentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array', 'max:4'],
            'filter.name' => ['sometimes', 'string', 'max:255'],
            'filter.sku' => ['sometimes', 'string', 'max:255'],
            'filter.quantity' => ['sometimes', 'integer'],
            'filter.sort_order' => ['sometimes', 'integer'],
            'sort' => ['sometimes', 'string'],
        ];
    }
}
