<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithBundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->resource;

        return [
            ...(new ProductResource($product))->toArray($request),
            $this->mergeWhen($product->is_bundle, [
                'bundle_items' => $product->productBundles->map(function (ProductBundle $productBundle) use($request): array {
                    /** @var Product $childProduct */
                    $childProduct = $productBundle->childProduct;

                    return (new ProductResource($childProduct))->toArray($request);
                }),
            ]),
        ];
    }
}
