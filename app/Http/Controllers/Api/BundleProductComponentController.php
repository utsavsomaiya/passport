<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BundleProductComponentRequest;
use App\Http\Requests\Api\FetchBundleProductComponentRequest;
use App\Http\Requests\Api\UpdateBundleProductComponentRequest;
use App\Http\Resources\Api\BundleProductComponentResource;
use App\Queries\BundleProductComponentQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class BundleProductComponentController extends Controller
{
    public function __construct(
        protected BundleProductComponentQueries $bundleProductComponentQueries,
    ) {

    }

    public function fetch(FetchBundleProductComponentRequest $request, string $parentProductId): AnonymousResourceCollection
    {
        $bundleProductComponents = $this->bundleProductComponentQueries->listQuery($request, $parentProductId);

        return BundleProductComponentResource::collection($bundleProductComponents);
    }

    public function add(BundleProductComponentRequest $request, string $parentProductId): JsonResponse
    {
        $this->bundleProductComponentQueries->create($request, $parentProductId);

        return Response::api('Component(s) have been added to the specified bundle product successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $isConvertedToRegularProduct = $this->bundleProductComponentQueries->delete($id);

        $message = 'The component of the specified bundle product was deleted successfully.';

        if ($isConvertedToRegularProduct) {
            $message .= 'The parent product has been converted to a regular product now as there are no product components left.';
        }

        return Response::api($message);
    }

    public function update(UpdateBundleProductComponentRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->bundleProductComponentQueries->update($validatedData, $id);

        return Response::api('The component of the bundle product has been updated successfully.');
    }
}
