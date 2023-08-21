<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Hierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HierarchyQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Hierarchy::class, $request)
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->allowedFilters([
                $this->filter('name'),
                $this->filter('id'),
            ])
            ->select('id', 'parent_hierarchy_id', 'name', 'description', 'slug', 'created_at')
            ->where('company_id', app('company_id'))
            ->with('children:id,parent_hierarchy_id,name,description,slug,created_at')
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

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
        abort_if($hierarchy->children_exists, Response::HTTP_NOT_ACCEPTABLE, sprintf('This hierarchy has children. Cannot be deleted - %s.', $hierarchy->name));

        resolve(HierarchyProductQueries::class)->deleteByHierarchyId($hierarchy->id);

        $hierarchy->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
