<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\FetchUserRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Queries\UserQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserQueries $userQueries
    ) {

    }

    public function fetch(FetchUserRequest $request): AnonymousResourceCollection
    {
        $users = $this->userQueries->listQuery($request);

        return UserResource::collection($users);
    }

    public function create(UserRequest $request): JsonResponse
    {
        $this->userQueries->create($request->validated());

        return Response::api('User created successfully.');
    }

    public function delete(string $id): JsonResponse
    {
        $this->userQueries->delete($id);

        Cache::forget('roles_and_permissions_of_user_' . $id);

        return Response::api('User deleted successfully.');
    }

    public function restore(string $id): JsonResponse
    {
        $this->userQueries->restore($id);

        return Response::api('User restored successfully.');
    }

    public function update(UserRequest $request, string $id): JsonResponse
    {
        $this->userQueries->update($request->validated(), $id);

        return Response::api('User updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userQueries->changePassword($request);

        return Response::api('Password updated successfully.');
    }
}
