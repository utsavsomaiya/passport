<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Api\FetchHierarchyProductRequest;
use App\Http\Requests\Api\HierarchyProductRequest;
use App\Http\Resources\Api\HierarchyProductResource;
use App\Queries\HierarchyProductQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class HierarchyProductController extends Controller
{
    public function __construct(
        protected HierarchyProductQueries $hierarchyProductQueries
    ) {

    }

    public function fetch(FetchHierarchyProductRequest $request, string $hierarchyId): HierarchyProductResource
    {
        $hierarchyWithProduct = $this->hierarchyProductQueries->listQuery($request, $hierarchyId);

        return new HierarchyProductResource($hierarchyWithProduct);
    }

    public function createOrUpdate(HierarchyProductRequest $request): JsonResponse
    {
        $this->hierarchyProductQueries->createOrUpdate($request);

        return Response::api('The product has been assigned to the specified hierarchy successfully.');
    }

    public function delete(string $hierarchyId, string $productId): JsonResponse
    {
        $this->hierarchyProductQueries->delete($hierarchyId, $productId);

        return Response::api('The product has been detached to the specified hierarchy successfully.');
    }
}