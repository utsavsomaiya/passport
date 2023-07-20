<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PriceBookRequest;
use App\Http\Resources\Api\PriceBookResource;
use App\Queries\PriceBookQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PriceBookController extends Controller
{
    public function __construct(
        protected PriceBookQueries $priceBookQueries
    ) {

    }

    public function fetch(): AnonymousResourceCollection
    {
        $priceBooks = $this->priceBookQueries->listQuery();

        return PriceBookResource::collection($priceBooks);
    }

    public function create(PriceBookRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->priceBookQueries->create($validatedData);

        return response()->json([
            'success' => __('Price book created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->priceBookQueries->delete($id);

        return response()->json([
            'success' => __('Price book deleted successfully.'),
        ]);
    }

    public function update(PriceBookRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->priceBookQueries->update($validatedData, $id);

        return response()->json([
            'success' => __('Price book updated successfully.'),
        ]);
    }
}
