<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttributeRequest;
use App\Http\Requests\Api\FetchAttributesRequest;
use App\Http\Resources\Api\AttributeResource;
use App\Queries\AttributeQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeQueries $attributeQueries
    ) {

    }

    public function fetch(FetchAttributesRequest $request, string $templateId = null): AnonymousResourceCollection
    {
        $request->validated();

        $attributes = $this->attributeQueries->listQuery($templateId);

        return AttributeResource::collection($attributes);
    }

    public function create(AttributeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->attributeQueries->create($validatedData);

        return response()->json([
            'success' => __('Attribute created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->attributeQueries->delete($id);

        return response()->json([
            'success' => __('Attribute deleted successfully'),
        ]);
    }

    public function update(AttributeRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->attributeQueries->update($validatedData, $id);

        return response()->json([
            'success' => __('Attribute updated successfully'),
        ]);
    }
}
