<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleProductComponentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bundleProductComponent = $this->resource;

        return [
            'component_id' => $bundleProductComponent->id,
            'component' => new ProductResource($bundleProductComponent->childProduct),
            'quantity' => $bundleProductComponent->quantity,
            'sort_order' => $bundleProductComponent->sort_order,
        ];
    }
}
