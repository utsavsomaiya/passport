<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class RoleQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Role::class, $request)
            ->allowedFilters([$this->filter('name')])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->select('id', 'name', 'description', 'created_at')
            ->where('company_id', app('company_id'))
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): Role
    {
        $data['company_id'] ??= app('company_id');

        return Role::create($data);
    }

    public function delete(string $id): void
    {
        abort_if(resolve(RoleUserQueries::class)->exists($id), Response::HTTP_NOT_ACCEPTABLE, 'This role is assigned to one or more users already. Cannot be deleted.');

        resolve(PermissionQueries::class)->deleteByRole($id);

        $role = Role::where('company_id', app('company_id'))->findOrFail($id);

        abort_if($role->name === 'Super Admin', Response::HTTP_BAD_REQUEST, 'This role can not be deleted');

        $role->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        Role::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
