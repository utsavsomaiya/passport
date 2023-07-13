<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttributeRequest;
use App\Http\Resources\Api\AttributeResource;
use App\Queries\AttributeQueries;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttributeController extends Controller
{
    public function __construct(
        protected AttributeQueries $attributeQueries
    ) {

    }

    public function fetch(string $templateId = null): AnonymousResourceCollection
    {
        $attributes = $this->attributeQueries->listQuery($templateId);

        return AttributeResource::collection($attributes->getCollection());
    }

    public function create(AttributeRequest $request): void
    {
        $validatedData = $request->validated();

        $this->attributeQueries->create($validatedData);
    }
}
