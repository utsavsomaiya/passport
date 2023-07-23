<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchPriceBookRequest;
use App\Http\Requests\Api\PriceBookRequest;
use App\Http\Resources\Api\PriceBookResource;
use App\Queries\PriceBookQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class PriceBookController extends Controller
{
    public function __construct(
        protected PriceBookQueries $priceBookQueries
    ) {

    }

    public function fetch(FetchPriceBookRequest $request): AnonymousResourceCollection
    {
        $priceBooks = $this->priceBookQueries->listQuery($request);

        return PriceBookResource::collection($priceBooks);
    }

    public function create(PriceBookRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->priceBookQueries->create($validatedData);

        return Response::api('Price book created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->priceBookQueries->delete($id);

        return Response::api('Price book deleted successfully.');
    }

    public function update(PriceBookRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->priceBookQueries->update($validatedData, $id);

        return Response::api('Price book updated successfully.');
    }
}
