<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchTemplateRequest;
use App\Http\Requests\Api\TemplateRequest;
use App\Http\Resources\Api\TemplateResource;
use App\Queries\TemplateQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateController extends Controller
{
    public function __construct(
        protected TemplateQueries $templateQueries
    ) {

    }

    public function fetch(FetchTemplateRequest $request): AnonymousResourceCollection
    {
        $templates = $this->templateQueries->listQuery($request);

        return TemplateResource::collection($templates);
    }

    public function create(TemplateRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->templateQueries->create($validatedData);

        return response()->json([
            'success' => __('Template created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->templateQueries->delete($id);

        return response()->json([
            'success' => __('Template deleted successfully.'),
        ]);
    }

    public function update(TemplateRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->templateQueries->update($validatedData, $id);

        return response()->json([
            'success' => __('Template updated successfully.'),
        ]);
    }
}
