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
        $hierarchyProductsCount = $this->hierarchyQueries->delete($id);

        $message = 'Hierarchy has been deleted successfully.';

        if ($hierarchyProductsCount > 0) {
            $message .= trans_choice(
                key: 'The product assigned to the hierarchy has been detached now.|The products assigned (:productCount) to the hierarchy have been detached now.',
                number: $hierarchyProductsCount,
                replace: ['productCount' => $hierarchyProductsCount]
            );
        }

        return Response::api($message);
    }

    public function update(HierarchyRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->hierarchyQueries->update($validatedData, $id);

        return Response::api('Hierarchy updated successfully.');
    }
}
