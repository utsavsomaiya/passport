<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            ...$this->productData($product),
            $this->mergeWhen($product->is_bundle, [
                'bundle_items' => $product->productBundles->map(function (ProductBundle $productBundle): ProductResource {
                    /** @var Product $product */
                    $product = $productBundle->product;

                    return new self($product);
                }),
            ]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'sku' => $product->sku,
            'upc_ean' => $product->upc_ean,
            'external_reference' => $product->external_reference,
            'status' => $product->status,
            'is_bundle' => $product->is_bundle,
            'created_at' => $product->created_at?->displayFormat(),
            'media' => $product->getMedia('product_images')->map(fn ($media): array => [
                'uploaded_at' => $media->created_at?->displayFormat(),
                'url' => $media->getUrl(),
            ]),
        ];
    }
}
