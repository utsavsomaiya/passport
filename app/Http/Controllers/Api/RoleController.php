<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchRoleRequest;
use App\Http\Requests\Api\RoleRequest;
use App\Http\Resources\Api\RoleResource;
use App\Queries\RoleQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    public function __construct(
        protected RoleQueries $roleQueries
    ) {

    }

    public function fetch(FetchRoleRequest $request): AnonymousResourceCollection
    {
        $roles = $this->roleQueries->listQuery($request);

        return RoleResource::collection($roles);
    }

    public function create(RoleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $role = $this->roleQueries->create($validatedData);

        return Response::api('Role created successfully.', [
            'role_id' => $role->id,
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->roleQueries->delete($id);

        return Response::api('Role deleted successfully');
    }

    public function update(RoleRequest $request, string $id): JsonResponse
    {
        $validatedData = $request->validated();

        $this->roleQueries->update($validatedData, $id);

        return Response::api('Role updated successfully');
    }
}
