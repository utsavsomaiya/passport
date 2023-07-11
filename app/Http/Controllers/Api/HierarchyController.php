<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HierarchyRequest;
use App\Http\Resources\Api\HierarchyResource;
use App\Queries\HierarchyQueries;

class HierarchyController extends Controller
{
    public function __construct(
        protected HierarchyQueries $hierarchyQueries
    ) {

    }

    public function fetch()
    {
        $hierarchies = $this->hierarchyQueries->listQuery();

        return HierarchyResource::collection($hierarchies->getCollection());
    }

    public function create(HierarchyRequest $request): void
    {

    }
}
