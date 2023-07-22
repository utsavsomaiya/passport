<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FetchUserRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Queries\UserQueries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserQueries $userQueries
    ) {

    }

    public function fetch(FetchUserRequest $request, string $roleId = null): AnonymousResourceCollection
    {
        $users = $this->userQueries->listQuery($request, $roleId);

        return UserResource::collection($users);
    }

    public function create(UserRequest $request): JsonResponse
    {
        $this->userQueries->create($request->validated());

        return response()->json([
            'success' => __('User created successfully.'),
        ]);
    }

    public function delete(string $id): JsonResponse
    {
        $this->userQueries->delete($id);

        return response()->json([
            'success' => __('User deleted successfully.'),
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $this->userQueries->restore($id);

        return response()->json([
            'success' => __('User restored successfully.'),
        ]);
    }

    public function update(UserRequest $request, string $id): JsonResponse
    {
        $this->userQueries->update($request->validated(), $id);

        return response()->json([
            'success' => __('User updated successfully.'),
        ]);
    }
}
