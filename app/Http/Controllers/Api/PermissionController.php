<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function fetch(): JsonResponse
    {
        $permissions = Permission::getFeatureGates()->mapWithKeys(fn ($name, $key): array => [
            $name => Str::of($name)->replaceFirst('-', ' ')->title()->value(),
        ])->toArray();

        return response()->json(['permissions' => $permissions]);
    }
}
