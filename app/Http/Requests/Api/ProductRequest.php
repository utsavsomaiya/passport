<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Product;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

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
            /** @var string $productId */
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
            'bundle_items' => [...$bundleItemRules, 'max:3'],
            'bundle_items.ids' => [
                ...$bundleItemRules,
                Rule::exists(Product::class, 'id')
                    ->where('company_id', app('company_id'))
                    ->where('is_bundle', false)
                    ->whereNot('id', $productId ??= ''),
            ],
            'bundle_items.ids.*' => ['required_with:bundle_items.ids', 'string', 'uuid'],
            'bundle_items.quantities' => $bundleItemRules,
            'bundle_items.quantities.*' => ['required_with:bundle_items.quantities', 'integer', 'gt:0'],
            'bundle_items.sort_orders' => ['sometimes', 'array'],
            'bundle_items.sort_orders.*' => ['required_with:bundle_items.sort_orders', 'integer'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->get('is_bundle') !== true) {
                    return;
                }

                $bundleItems = $this->get('bundle_items');

                if (is_array($bundleItems)) {
                    if (! array_key_exists('ids', $bundleItems)) {
                        return;
                    }

                    if (! array_key_exists('quantities', $bundleItems)) {
                        return;
                    }

                    $ids = $bundleItems['ids'];
                    $quantities = $bundleItems['quantities'];

                    if (! is_array($ids)) {
                        return;
                    }

                    if (! is_array($quantities)) {
                        return;
                    }

                    if (count($ids) !== count(array_unique($ids))) {
                        $validator->errors()->add('bundle_items.ids', 'The bundle items must be different.');
                    }

                    if (count($ids) === count($quantities)) {
                        return;
                    }

                    $validator->errors()->add('bundle_items', 'Bundle Items and quantity arrays do not match.');
                }
            }
        ];
    }
}
