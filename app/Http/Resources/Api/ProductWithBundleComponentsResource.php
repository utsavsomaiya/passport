<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\BundleProductComponent;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithBundleComponentsResource extends JsonResource
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
            ...resolve(ProductResource::class, ['resource' => $product])->toArray($request),
            $this->mergeWhen($product->is_bundle, [
                'bundle_components' => BundleProductComponentResource::collection($product->bundleComponents),
            ]),
        ];
    }
}
