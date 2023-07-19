<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Api\PermissionRequest;
use App\Queries\PermissionQueries;
use Illuminate\Http\JsonResponse;

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
