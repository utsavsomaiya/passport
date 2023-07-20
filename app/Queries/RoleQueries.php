<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class RoleQueries extends GlobalQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters([$this->filter('name')])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->select('id', 'name', 'description', 'created_at')
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function delete(string $id): void
    {
        abort_if(resolve(RoleUserQueries::class)->exists($id), Response::HTTP_NOT_ACCEPTABLE, 'This role is assigned to one or more users already. Cannot be deleted.');

        Role::query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        Role::query()
            ->where('id', $id)
            ->update($data);
    }
}
