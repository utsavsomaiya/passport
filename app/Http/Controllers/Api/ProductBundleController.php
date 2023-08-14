<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchProductRequest;
use App\Http\Requests\Api\ProductBundleRequest;
use App\Http\Requests\Api\UpdateProductBundleRequest;
use App\Http\Resources\ProductBundleResource;
use App\Queries\ProductBundleQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class ProductBundleController extends Controller
{
    public function __construct(
        protected ProductBundleQueries $productBundleQueries
    ) {

    }

    public function fetch(FetchProductRequest $request, string $productId): AnonymousResourceCollection
    {
        $bundleProducts = $this->productBundleQueries->listQuery($request, $productId);

        return ProductBundleResource::collection($bundleProducts);
    }

    public function create(ProductBundleRequest $request, string $productId): JsonResponse
    {
        $this->productBundleQueries->create($request, $productId);

        return Response::api('Product bundle created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->productBundleQueries->delete($id);

        return Response::api('Product bundle deleted successfully.');
    }

    public function update(UpdateProductBundleRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->productBundleQueries->update($validatedData, $id);

        return Response::api('Product bundle updated successfully.');
    }
}
