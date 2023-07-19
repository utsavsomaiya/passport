<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function fetch(): JsonResponse
    {
        return response()->json([
            'permissions' => Permission::getFeatureGates()->toArray(),
        ]);
    }
}
