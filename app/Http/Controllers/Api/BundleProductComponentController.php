<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchBundleProductComponentRequest;
use App\Http\Resources\Api\BundleProductComponentResource;
use App\Queries\BundleProductComponentQueries;

class BundleProductComponentController extends Controller
{
    public function __construct(
        protected BundleProductComponentQueries $bundleProductComponentQueries,
    ) {

    }

    public function fetch(FetchBundleProductComponentRequest $request, string $parentProductId)
    {
        $bundleProductComponents = $this->bundleProductComponentQueries->listQuery($request, $parentProductId);

        return BundleProductComponentResource::collection($bundleProductComponents);
    }
}
