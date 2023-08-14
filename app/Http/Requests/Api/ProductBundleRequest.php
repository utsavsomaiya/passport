<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductBundleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int|string, mixed>>
     */
    public function rules(): array
    {
        /** @var string $parentProductId */
        $parentProductId = $this->route()?->parameter('productId');

        $childProductIdRules = [
            'string',
            'uuid',
            Rule::exists(Product::class, 'id')
                ->where('company_id', app('company_id')) // Current Company products only
                ->where('is_bundle', false) // Non-bundle Product allows
                ->whereNot('id', $parentProductId), // Parent Product doesn't allow
        ];

        return [
            'bundle_products' => ['required', 'array'],
            'bundle_products.0' => ['required', 'array', 'min:2', 'max:3'],
            'bundle_products.*' => ['sometimes', 'array', 'min:2', 'max:3'],
            'bundle_products.0.id' => ['required', ...$childProductIdRules],
            'bundle_products.*.id' => ['sometimes', ...$childProductIdRules],
            'bundle_products.0.quantity' => ['required', ...$quantityRules = ['integer', 'gt:0']],
            'bundle_products.*.quantity' => ['sometimes', ...$quantityRules],
            'bundle_products.*.sort_order' => ['sometimes', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bundle_products.*' => 'bundle_products #:position',
        ];
    }
}
