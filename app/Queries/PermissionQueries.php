<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Permission;

class PermissionQueries
{
    /**
     * @param  array<string, string|array<int, string>>  $data
     */
    public function givePermissions(array $data): void
    {
        /** @var array<int, string> $permissions */
        $permissions = $data['permissions'];

        $insertData = [];

        foreach ($permissions as $permission) {
            $insertData[] = [
                'role_id' => $data['role'],
                'title' => $permission,
            ];
        }

        Permission::upsert($insertData, ['role_id', 'title']);
    }

    /**
     * @param  array<string, string|array<int, string>>  $data
     */
    public function revokePermissions(array $data): void
    {
        Permission::query()
            ->where('role_id', $data['role'])
            ->whereIn('title', $data['permissions'])
            ->delete();
    }
}
