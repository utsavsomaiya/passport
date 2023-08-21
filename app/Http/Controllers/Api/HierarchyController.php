<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchHierarchyRequest;
use App\Http\Requests\Api\HierarchyRequest;
use App\Http\Resources\Api\HierarchyResource;
use App\Queries\HierarchyQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class HierarchyController extends Controller
{
    public function __construct(
        protected HierarchyQueries $hierarchyQueries
    ) {

    }

    public function fetch(FetchHierarchyRequest $request): AnonymousResourceCollection
    {
        $hierarchies = $this->hierarchyQueries->listQuery($request);

        return HierarchyResource::collection($hierarchies);
    }

    public function create(HierarchyRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->hierarchyQueries->create($validatedData);

        return Response::api('Hierarchy created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->hierarchyQueries->delete($id);

        return Response::api('Hierarchy has been successfully deleted. If it was assigned to the product, it has been automatically removed.');
    }

    public function update(HierarchyRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->hierarchyQueries->update($validatedData, $id);

        return Response::api('Hierarchy updated successfully.');
    }
}
