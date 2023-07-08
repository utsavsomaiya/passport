<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $locale = $this->resource;

        return [
            'id' => $locale->id,
            'name' => $locale->name,
            'code' => $locale->code,
            'status' => $locale->status,
        ];
    }
}
