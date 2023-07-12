<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Hierarchy;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HierarchyQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Hierarchy::class)
            ->allowedFields(['id', 'name', 'description', 'slug', 'parent_hierarchy_id'])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name'])
            ->allowedFilters(['name', 'slug'])
            ->whereNull('parent_hierarchy_id')
            ->where('company_id', app('company_id'))
            ->with('childHierarchies')
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        $hierarchyDoesntExist = Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $data['parent_hierarchy_id'])
            ->doesntExist();

        if ($data['parent_hierarchy_id'] && $hierarchyDoesntExist) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Parent hierarchy does not exists in database');
        }

        Hierarchy::create($data);
    }

    /**
     * @throws HttpException
     */
    public function delete(string $id): void
    {
        $hierarchy = Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->withExists('childHierarchies')
            ->firstOrFail();

        // @phpstan-ignore-next-line
        abort_if($hierarchy->child_hierarchies_exists > 0, Response::HTTP_NOT_ACCEPTABLE, sprintf('This `%s` has children available', $hierarchy->name));

        $hierarchy->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        $data['company_id'] ??= app('company_id');

        Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
