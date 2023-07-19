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

        /** @var array<int, string> $roles*/
        $roles = $data['roles'];

        $insertData = [];

        foreach ($roles as $role) {
            $insertData[] = [
                'role_id' => $role,
                'user_id' => $user,
            ];
        }

        RoleUser::upsert($insertData, ['role_id', 'user_id']);
    }

    /**
     * @param  array<string, string|array<int, string>>  $data
     */
    public function removeRoles(array $data): void
    {
        RoleUser::query()
            ->where('user_id', $data['user'])
            ->whereIn('role_id', $data['roles'])
            ->delete();
    }
}
