<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $priceBook = $this->resource;

        return [
            'id' => $priceBook->id,
            'name' => $priceBook->name,
            'description' => $priceBook->description,
        ];
    }
}
