<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HierarchyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hierarchy = $this->resource;

        return [
            'id' => $hierarchy->id,
            'name' => $hierarchy->name,
            'description' => $hierarchy->description,
            'slug' => $hierarchy->slug,
            'parent_hierarchy_id' => $hierarchy->parent_hierarchy_id,
            'created_at' => $hierarchy->created_at?->format(config('app.datetime_display_format')),
            'children' => self::collection($hierarchy->children),
        ];
    }
}
