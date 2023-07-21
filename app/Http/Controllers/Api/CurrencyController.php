<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CurrencyRequest;
use App\Http\Requests\Api\FetchCurrencyRequest;
use App\Http\Resources\Api\CurrencyResource;
use App\Queries\CurrencyQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyQueries $currencyQueries
    ) {
    }

    public function fetch(FetchCurrencyRequest $request): AnonymousResourceCollection
    {
        $request->validated();

        $currencies = $this->currencyQueries->listQuery($request);

        return CurrencyResource::collection($currencies);
    }

    public function create(CurrencyRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->currencyQueries->create($validatedData);

        return response()->json([
            'success' => __('Currency created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->currencyQueries->delete($id);

        return response()->json([
            'success' => __('Currency deleted successfully.'),
        ]);
    }

    public function update(CurrencyRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->currencyQueries->update($id, $validatedData);

        return response()->json([
            'success' => __('Currency updated successfully.'),
        ]);
    }
}
