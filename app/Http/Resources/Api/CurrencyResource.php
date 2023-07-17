<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currency = $this->resource;

        return [
            'id' => $currency->id,
            'code' => $currency->code,
            'exchange_rate' => $currency->exchange_rate,
            'format' => $currency->format,
            'decimal_places' => $currency->decimal_places,
            'decimal_point' => $currency->decimal_point,
            'thousand_separator' => $currency->thousand_separator,
            'is_default' => $currency->is_default,
            'status' => $currency->status,
            'created_at' => $currency->created_at?->displayFormat(),
        ];
    }
}
