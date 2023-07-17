<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Queries\UserQueries;

class UserController extends Controller
{
    public function __construct(
        protected UserQueries $userQueries
    ) {

    }

    public function fetch()
    {
        $users = $this->userQueries->listQuery();

        return UserResource::collection($users->getCollection());
    }

    public function create()
    {

    }
}
