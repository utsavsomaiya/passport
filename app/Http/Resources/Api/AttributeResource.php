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

        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'description' => $attribute->description,
            'template_name' => $attribute->template?->name,
            'field_type' => $attribute->field_type?->resourceName(),
            'field_description' => $attribute->field_type?->description(),
            'validation' => [
                'from' => $attribute->from,
                'to' => $attribute->to,
            ],
            $this->mergeWhen(in_array($attribute->field_type, FieldType::selections()), [
                'field_options' => $attribute->options,
            ]),
            'is_required' => $attribute->is_required,
            'status' => $attribute->status,
            'created_at' => $attribute->created_at?->displayFormat(),
        ];
    }
}
