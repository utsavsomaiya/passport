<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoleUserRequest;
use App\Queries\RoleUserQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json([
            'success' => __('Roles assigned successfully.'),
        ]);
    }
}
