<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Hierarchy;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

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
}
