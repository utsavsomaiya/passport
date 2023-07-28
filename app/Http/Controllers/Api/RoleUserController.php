<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoleUserRequest;
use App\Jobs\ForgetUsersCacheEntriesJob;
use App\Queries\RoleUserQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class RoleUserController extends Controller
{
    public function __construct(
        protected RoleUserQueries $roleUserQueries
    ) {

    }

    public function assignRoles(RoleUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->roleUserQueries->assignRoles($validatedData);

        ForgetUsersCacheEntriesJob::dispatch();

        return Response::api('Roles assigned successfully.');
    }

    public function dissociateRoles(RoleUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->roleUserQueries->removeRoles($validatedData);

        ForgetUsersCacheEntriesJob::dispatch();

        return Response::api('Roles removed successfully.');
    }
}
