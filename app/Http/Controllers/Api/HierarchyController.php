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

class HierarchyController extends Controller
{
    public function __construct(
        protected HierarchyQueries $hierarchyQueries
    ) {

    }

    public function fetch(FetchHierarchyRequest $request): AnonymousResourceCollection
    {
        $request->validated();

        $hierarchies = $this->hierarchyQueries->listQuery($request);

        return HierarchyResource::collection($hierarchies);
    }

    public function create(HierarchyRequest $request, string $parent = null): JsonResponse
    {
        $validatedData = $request->validated();

        $validatedData['parent_hierarchy_id'] = $parent;

        $this->hierarchyQueries->create($validatedData);

        return response()->json([
            'success' => __('Hierarchy created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->hierarchyQueries->delete($id);

        return response()->json([
            'success' => __('Hierarchy deleted successfully.'),
        ]);
    }

    public function update(HierarchyRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->hierarchyQueries->update($validatedData, $id);

        return response()->json([
            'success' => __('Hierarchy updated successfully.'),
        ]);
    }
}
