<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductBundleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'gt:0'],
            'sort_order' => ['sometimes', 'integer'],
        ];
    }
}
