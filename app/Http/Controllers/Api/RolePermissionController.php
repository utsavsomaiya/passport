<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PermissionRequest;
use App\Jobs\ForgetUsersCacheEntriesJob;
use App\Queries\PermissionQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class RolePermissionController extends Controller
{
    public function __construct(
        protected PermissionQueries $permissionQueries
    ) {

    }

    public function givePermissions(PermissionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->permissionQueries->givePermissions($validatedData);

        ForgetUsersCacheEntriesJob::dispatch('roles_and_permissions_of_user_');

        return Response::api('Permission given successfully.');
    }

    public function revokePermissions(PermissionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->permissionQueries->revokePermissions($validatedData);

        ForgetUsersCacheEntriesJob::dispatch('roles_and_permissions_of_user_');

        return Response::api('Permission revoked successfully.');
    }
}
