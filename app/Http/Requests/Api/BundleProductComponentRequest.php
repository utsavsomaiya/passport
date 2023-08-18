<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BundleProductComponentRequest extends FormRequest
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
                ->where('company_id', app('company_id')) // Current company products only
                ->where('is_bundle', false) // Non-bundle product allows
                ->whereNot('id', $parentProductId), // The child cannot be same as parent
        ];

        return [
            'bundle_product_components' => ['required', 'array'],
            'bundle_product_components.0' => ['required', 'array', 'min:2', 'max:3'],
            'bundle_product_components.*' => ['sometimes', 'array', 'min:2', 'max:3'],
            'bundle_product_components.0.id' => ['required', ...$childProductIdRules],
            'bundle_product_components.*.id' => ['sometimes', ...$childProductIdRules],
            'bundle_product_components.0.quantity' => ['required', ...$quantityRules = ['integer', 'gt:0']],
            'bundle_product_components.*.quantity' => ['sometimes', ...$quantityRules],
            'bundle_product_components.*.sort_order' => ['sometimes', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bundle_product_components.*' => 'bundle_product_components #:position',
            'bundle_product_components.*.id' => 'bundle_product_components #:position has id',
            'bundle_product_components.*.quantity' => 'bundle_product_components #:position has quantity',
            'bundle_product_components.*.sort_order' => 'bundle_product_components #:position has sort_order',
        ];
    }
}
