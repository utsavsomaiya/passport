<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductBundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $productBundle = $this->resource;

        return [
            ...((new ProductResource($productBundle->childProduct))->toArray($request)),
            'quantity' => $productBundle->quantity,
            'sort_order' => $productBundle->sort_order,
        ];
    }
}
