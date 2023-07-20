<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LocaleRequest;
use App\Http\Resources\Api\LocaleResource;
use App\Queries\LocaleQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocaleController extends Controller
{
    public function __construct(
        protected LocaleQueries $localeQueries
    ) {

    }

    public function fetch(Request $request): AnonymousResourceCollection
    {
        $locales = $this->localeQueries->listQuery();

        return LocaleResource::collection($locales);
    }

    public function create(LocaleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->localeQueries->create($validatedData);

        return response()->json([
            'success' => __('Locale created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->localeQueries->delete($id);

        return response()->json([
            'success' => __('Locale deleted successfully.'),
        ]);
    }

    public function update(LocaleRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->localeQueries->update($id, $validatedData);

        return response()->json([
            'success' => __('Locale updated successfully.'),
        ]);
    }
}
