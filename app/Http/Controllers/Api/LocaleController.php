<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchLocaleRequest;
use App\Http\Resources\Api\LocaleResource;
use App\Queries\LocaleQueries;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
