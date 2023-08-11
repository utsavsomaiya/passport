<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchProductRequest;
use App\Http\Requests\Api\ProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\ProductWithBundleResource;
use App\Queries\ProductQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductQueries $productQueries
    ) {

    }

    public function fetch(FetchProductRequest $request): AnonymousResourceCollection
    {
        $products = $this->productQueries->listQuery($request);

        return ProductWithBundleResource::collection($products);
    }

    public function create(ProductRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->productQueries->create($validatedData);

        return Response::api('Product created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->productQueries->delete($id);

        return Response::api('Product deleted successfully');
    }

    public function update(ProductRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->productQueries->update($id, $validatedData);

        return Response::api('Product updated successfully');
    }
}
