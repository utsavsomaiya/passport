<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckCredentialsRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Queries\CompanyQueries;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyQueries $companyQueries
    ) {

    }

    public function __invoke(CheckCredentialsRequest $request): AnonymousResourceCollection
    {
        $validatedData = $request->validated();

        $companies = $this->companyQueries->listQuery($validatedData['user']);

        return CompanyResource::collection($companies);
    }
}
