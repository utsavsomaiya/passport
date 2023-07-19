<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class UserQueries extends GlobalQueries
{
    public function listQuery(?string $roleId): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFields(['first_name', 'last_name', 'username', 'email', 'created_at'])
            ->allowedFilters([
                $this->filter('first_name'),
                $this->filter('last_name'),
                $this->filter('username'),
                $this->filter('email'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['first_name', 'last_name', 'created_at'])
            ->mergeSelect('id')
            ->with([
                'tokens:id,tokenable_id,tokenable_type,last_used_at',
                'roles:id,name',
            ])
            ->when($roleId, function ($query) use ($roleId): void {
                $query->whereHas('roles', function ($query) use ($roleId): void {
                    $query->where('role_id', $roleId);
                });
            })
            ->jsonPaginate();
    }

    public function delete(string $id): void
    {
        User::query()->where('id', $id)->delete(); // Soft delete
    }

    public function restore(string $id): void
    {
        User::withTrashed()->where('id', $id)->restore();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        User::create($data);
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        User::query()
            ->where('id', $id)
            ->update($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->firstWhere('email', $email);
    }
}
