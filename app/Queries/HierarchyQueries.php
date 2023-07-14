<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Hierarchy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HierarchyQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Hierarchy::class)
            ->select(['id', 'name', 'description', 'slug', 'parent_hierarchy_id'])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name'])
            ->allowedFilters([
                AllowedFilter::callback('name', function (Builder $query, $value): void {
                    $query->where('name', '=', $value)
                        ->orWhereHas('children', function ($query) use ($value): void {
                            $query->where('name', '=', $value);
                        });
                }),
            ])
            ->whereNull('parent_hierarchy_id')
            ->where('company_id', app('company_id'))
            ->with('children')
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        $hierarchyExists = Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $data['parent_hierarchy_id'])
            ->exists();

        if ($data['parent_hierarchy_id'] && ! $hierarchyExists) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Parent hierarchy does not exist in database');
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
            ->withExists('children')
            ->firstOrFail();

        // @phpstan-ignore-next-line
        abort_if($hierarchy->children_exists, Response::HTTP_NOT_ACCEPTABLE, sprintf('This hierarchy has children. Cannot be deleted %s.', $hierarchy->name));

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
