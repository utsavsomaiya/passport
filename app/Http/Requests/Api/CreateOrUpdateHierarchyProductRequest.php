<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Hierarchy;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

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
}
