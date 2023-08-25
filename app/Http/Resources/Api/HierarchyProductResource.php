<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HierarchyProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hierarchy = $this->resource;

        return [
            ...$hierarchy->products->map(fn (Product $product): array => [
                ...resolve(ProductResource::class, ['resource' => $product])->toArray($request),
                'is_curated_product' => $product->pivot->is_curated_product, // @phpstan-ignore-line
            ]),
        ];
    }
}
