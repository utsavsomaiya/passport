<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchCompanyRequest;
use App\Http\Requests\Api\SetCompanyRequest;
use App\Http\Resources\Api\CompanyResource;
use App\Queries\CompanyQueries;
use Facades\Laravel\Passport\PersonalAccessTokenFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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

    /**
     * @throws BadRequestException
     */
    public function setCompany(SetCompanyRequest $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();

        $token = PersonalAccessTokenFactory::findAccessToken(['access_token' => $bearerToken]);

        if ($token->exists) {
            $token->company_id = $request->company_id;

            $token->save();

            return Response::api('Company set successfully. You may access other API endpoints now.');
        }

        throw new BadRequestException('This bearer token cannot support in the system.');
    }
}
