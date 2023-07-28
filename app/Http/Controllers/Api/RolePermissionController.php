<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PermissionRequest;
use App\Jobs\FlushCaching;
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

        FlushCaching::dispatch();

        return Response::api('Permission given successfully.');
    }

    public function revokePermissions(PermissionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->permissionQueries->revokePermissions($validatedData);

        FlushCaching::dispatch();

        return Response::api('Permission revoked successfully.');
    }
}
