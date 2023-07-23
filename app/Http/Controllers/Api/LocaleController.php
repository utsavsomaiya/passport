<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchLocaleRequest;
use App\Http\Requests\Api\LocaleRequest;
use App\Http\Resources\Api\LocaleResource;
use App\Queries\LocaleQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class LocaleController extends Controller
{
    public function __construct(
        protected LocaleQueries $localeQueries
    ) {

    }

    public function fetch(FetchLocaleRequest $request): AnonymousResourceCollection
    {
        $locales = $this->localeQueries->listQuery($request);

        return LocaleResource::collection($locales);
    }

    public function create(LocaleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->localeQueries->create($validatedData);

        return Response::api('Locale created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->localeQueries->delete($id);

        return Response::api('Locale deleted successfully.');
    }

    public function update(LocaleRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->localeQueries->update($id, $validatedData);

        return Response::api('Locale updated successfully.');
    }
}
