<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\RoleUser;

class RoleUserQueries
{
    public function exists(string $roleId): bool
    {
        return RoleUser::query()->where('role_id', $roleId)->exists();
    }

    /**
     * @param  array<string, string|array<int, string>>  $data
     */
    public function assignRoles(array $data): void
    {
        $user = $data['user'];
        $roles = $data['roles'];

        $insertData = [];

        foreach ($roles as $roleId) {
            $insertData[] = [
                'role_id' => $roleId,
                'user_id' => $user,
            ];
        }

        RoleUser::upsert($insertData, ['role_id', 'user_id']);
    }
}
