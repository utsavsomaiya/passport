<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LocaleResource;
use App\Queries\LocaleQueries;
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
        $filterData = [
            'per_page' => $request->get('per_page', 12),
            'sort_by' => $request->get('sort_by'),
            'sort_direction' => $request->get('sort_direction'),
            'company_id' => app('company_id'),
        ];

        $locales = $this->localeQueries->listQuery($filterData);

        return LocaleResource::collection($locales->getCollection());
    }

    public function create(Request $request): void
    {

    }

    public function delete(string $id, Request $request): void
    {

    }

    public function update(string $id, Request $request): void
    {

    }
}
