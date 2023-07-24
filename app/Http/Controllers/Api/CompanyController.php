<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchCompanyRequest;
use App\Http\Requests\Api\SetCompanyRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Models\PersonalAccessToken;
use App\Queries\CompanyQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyQueries $companyQueries
    ) {

    }

    public function fetchCompanies(FetchCompanyRequest $request): AnonymousResourceCollection
    {
        $companies = $this->companyQueries->listQuery($request);

        return CompanyResource::collection($companies);
    }

    public function setCompany(SetCompanyRequest $request): JsonResponse
    {
        /** @var string $bearerToken */
        $bearerToken = $request->bearerToken();

        [$id, $token] = explode('|', $bearerToken, 2);

        /** @var PersonalAccessToken $token */
        $token = $request->user()?->tokens->find($id);

        $token->company_id = $request->company_id;

        $token->save();

        return Response::api('Company set successfully. You may access other API endpoints now.');
    }
}
