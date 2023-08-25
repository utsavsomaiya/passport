<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Facades\App\Enums\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function fetch(): JsonResponse
    {
        $permissions = Permission::listOfPermissions();

        return response()->json(['permissions' => $permissions]);
    }
}
