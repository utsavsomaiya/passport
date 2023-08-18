<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BundleProductComponentRequest;
use App\Http\Requests\Api\FetchBundleProductComponentRequest;
use App\Http\Requests\Api\UpdateBundleProductComponentRequest;
use App\Http\Resources\Api\BundleProductComponentResource;
use App\Queries\BundleProductComponentQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class BundleProductComponentController extends Controller
{
    public function __construct(
        protected BundleProductComponentQueries $bundleProductComponentQueries,
    ) {

    }

    public function fetch(FetchBundleProductComponentRequest $request, string $parentProductId): AnonymousResourceCollection
    {
        $bundleProductComponents = $this->bundleProductComponentQueries->listQuery($request, $parentProductId);

        return BundleProductComponentResource::collection($bundleProductComponents);
    }

    public function create(BundleProductComponentRequest $request, string $parentProductId): JsonResponse
    {
        $this->bundleProductComponentQueries->create($request, $parentProductId);

        return Response::api('Bundle of product components created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->bundleProductComponentQueries->delete($id);

        return Response::api('Bundle of product component successfully deleted. If the last product was part of the bundle, it has been automatically unbundled.');
    }

    public function update(UpdateBundleProductComponentRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->bundleProductComponentQueries->update($validatedData, $id);

        return Response::api('Bundle of product component updated successfully.');
    }
}
