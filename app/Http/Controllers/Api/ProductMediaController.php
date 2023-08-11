<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductMediaRequest;
use App\Http\Resources\Api\ProductMediaResource;
use App\Queries\ProductMediaQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class ProductMediaController extends Controller
{
    public function __construct(
        protected ProductMediaQueries $productMediaQueries
    ) {

    }

    public function fetch(string $productId): AnonymousResourceCollection
    {
        $media = $this->productMediaQueries->listQuery($productId);

        return ProductMediaResource::collection($media);
    }

    public function create(ProductMediaRequest $request, string $productId): JsonResponse
    {
        $this->productMediaQueries->create($request, $productId);

        return Response::api('Product image(s) have been uploaded successfully.');
    }

    public function delete(string $productId, string $id): JsonResponse
    {
        $this->productMediaQueries->delete($productId, $id);

        return Response::api('The product image has been deleted successfully.');
    }
}
