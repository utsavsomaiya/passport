<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HierarchyRequest;
use App\Http\Resources\Api\HierarchyResource;
use App\Queries\HierarchyQueries;
use Illuminate\Http\JsonResponse;

class HierarchyController extends Controller
{
    public function __construct(
        protected HierarchyQueries $hierarchyQueries
    ) {

    }

    public function fetch()
    {
        $hierarchies = $this->hierarchyQueries->listQuery();

        return HierarchyResource::collection($hierarchies->getCollection());
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
}
