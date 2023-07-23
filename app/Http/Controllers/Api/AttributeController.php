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
use Illuminate\Support\Facades\Response;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeQueries $attributeQueries
    ) {

    }

    public function fetch(FetchAttributesRequest $request): AnonymousResourceCollection
    {
        $attributes = $this->attributeQueries->listQuery($request);

        return AttributeResource::collection($attributes);
    }

    public function create(AttributeRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->attributeQueries->create($validatedData);

        return Response::api('Attribute created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->attributeQueries->delete($id);

        return Response::api('Attribute deleted successfully');
    }

    public function update(AttributeRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->attributeQueries->update($validatedData, $id);

        return Response::api('Attribute updated successfully');
    }
}
