<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PermissionRequest;
use App\Permission;
use App\Queries\PermissionQueries;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionQueries $permissionQueries
    ) {

    }

    public function fetch(): JsonResponse
    {
        return response()->json([
            'permissions' => Permission::getFeatureGates()->toArray(),
        ]);
    }

    public function givePermissions(PermissionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->permissionQueries->givePermissions($validatedData);

        return response()->json([
            'success' => __('Permission given successfully.'),
        ]);
    }

    public function revokePermissions(PermissionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $this->permissionQueries->revokePermissions($validatedData);

        return response()->json([
            'success' => __('Permission revoked successfully.'),
        ]);
    }
}
