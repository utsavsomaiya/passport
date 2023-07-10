<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\CompanyResource;

class CompanyController extends Controller
{
    public function __invoke(UserRequest $request)
    {
        $validatedData = $request->validated();

        return CompanyResource::collection($validatedData['user']->companies);
    }
}
