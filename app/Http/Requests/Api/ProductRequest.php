<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Unique;

class ProductRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => (bool) $this->status,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<int, string | Unique>|string>
     */
    public function rules(): array
    {
        $productId = null;

        if ($this->route()?->getName() === 'api.products.update') {
            $productId = $this->route()->parameter('id');
        }

        return [
            'parent_product_id' => ['sometimes', 'uuid', Rule::exists(Product::class)],
            'name' => ['required', 'string', 'max:255', Rule::unique(Product::class)->ignore($productId)->where('company_id', app('company_id'))],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', Rule::unique(Product::class)->ignore($productId)],
            'upc_ean' => ['nullable', 'string', 'max:255', Rule::unique(Product::class)->ignore($productId)],
            'external_reference' => ['nullable', 'string', 'url'],
            'status' => ['required', 'boolean'],
            'is_bundle' => ['required', 'boolean'],
            'images' => ['sometimes', 'array'],
            'images.0' => [$this->get('status') === true ? 'required' : 'sometimes', File::defaults()],
            'images.*' => ['sometimes', File::defaults()],
        ];
    }
}
