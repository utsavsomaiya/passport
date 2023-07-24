<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function fetch(): JsonResponse
    {
        $permissions = Permission::listOfPermissions();

        return response()->json(['permissions' => $permissions]);
    }
}
