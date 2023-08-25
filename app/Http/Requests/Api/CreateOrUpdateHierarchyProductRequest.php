<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Hierarchy;
use App\Models\Product;
use App\Queries\HierarchyProductQueries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Validator;

class CreateOrUpdateHierarchyProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Exists>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', Rule::exists(Product::class, 'id')->where('company_id', app('company_id'))],
            'hierarchy_id' => ['required', 'uuid', Rule::exists(Hierarchy::class, 'id')->where('company_id', app('company_id'))],
            'is_curated_product' => ['required', 'boolean'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $isCuratedProduct = match ($this->is_curated_product) {
                    '1', 'true', true => true,
                    default => false,
                };

                if (! resolve(HierarchyProductQueries::class)->isGreaterThanTwentyProductsAreCurated($this->hierarchy_id)) {
                    return;
                }

                if (! $isCuratedProduct) {
                    return;
                }

                $validator->errors()->add('product_id', 'The maximum number of curated products is 20. If you want to assign the hierarchy then change the `is_curated_product` to false');
            },
        ];
    }
}
