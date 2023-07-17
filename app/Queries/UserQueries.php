<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserQueries extends GlobalQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFields(['first_name', 'last_name', 'username', 'email', 'created_at'])
            ->allowedFilters([
                AllowedFilter::callback('name', function (Builder $query, $value): void {
                    $name = explode(' ', $value, 2);
                    $query->where('first_name', 'LIKE', '%' . $name[0] . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $name[0] . '%');
                }),
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
