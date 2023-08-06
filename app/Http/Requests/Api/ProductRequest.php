<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\RequiredIf;
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
            'is_bundle' => (bool) $this->is_bundle,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Exists|File|Unique|RequiredIf|string|null>>
     */
    public function rules(): array
    {
        $productId = null;

        $bundleItemRules = [Rule::requiredIf($this->get('is_bundle') === true), 'array'];

        if ($this->route()?->getName() === 'api.products.update') {
            $productId = $this->route()->parameter('id');
        }

        return [
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
            'bundle_items' => [...$bundleItemRules, Rule::exists(Product::class, 'id')],
            'bundle_items.*' => ['required_with:bundle_items', 'string', 'uuid'],
            'quantities' => $bundleItemRules,
            'quantities.*' => ['required_with:quantities', 'integer'],
            'sort_orders' => $bundleItemRules,
            'sort_orders.*' => ['required_with:sort_orders', 'integer'],
        ];
    }

    public function after()
    {
        
    }
}
