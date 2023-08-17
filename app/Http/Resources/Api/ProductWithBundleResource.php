<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

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
        ];
    }
}
