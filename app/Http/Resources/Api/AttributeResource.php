<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Enums\FieldType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attribute = $this->resource;

        if ($attribute == null) {
            dd($attribute);
        }

        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'description' => $attribute->description ?? 'N/A',
            'template_name' => $attribute->template?->name,
            'field_type' => $attribute->field_type?->resourceName(),
            'field_description' => $attribute->field_type?->description(),
            $this->mergeWhen(in_array($attribute->field_type, FieldType::selections()), [
                'field_options' => $attribute->options,
            ]),
            'created_at' => $attribute->created_at?->format('d F Y, h:i A'),
        ];
    }
}
