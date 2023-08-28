<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Hierarchy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
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
                'id',
                AllowedFilter::callback('product_id', function (Builder $query, $value): void {
                    $query->whereHas('products', function (Builder $query) use ($value): void {
                        $query->where('id', $value);
                    });
                }),
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
    public function delete(string $id): int
    {
        $hierarchy = Hierarchy::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->withCount('products')
            ->withExists('children')
            ->firstOrFail();

        // @phpstan-ignore-next-line
        abort_if($hierarchy->children_exists, Response::HTTP_NOT_ACCEPTABLE, sprintf('This hierarchy has children. Cannot be deleted - %s.', $hierarchy->name));

        DB::transaction(function () use ($hierarchy): void {
            resolve(HierarchyProductQueries::class)->deleteByHierarchyId($hierarchy->id);
            $hierarchy->delete();
        });

        return $hierarchy->products_count;
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

    /**
     * @throws ModelNotFoundException<Hierarchy>
     */
    public function fetchProductsWithCurated(Request $request, string $id): Model
    {
        return QueryBuilder::for(Hierarchy::query(), $request)
            ->allowedFilters([
                AllowedFilter::callback('curated_products_only', function (Builder $query, $value, $property): void {
                    $value = match ($value) {
                        '1', 'true', true => true,
                        default => false,
                    };

                    $query->whereHas('products', function (Builder $query) use ($value, $property): void {
                        $query->where($property, $value);
                    });
                }),
            ])
            ->select('id')
            ->with('products', function ($query): void {
                $query->with('media:id,file_name,model_id,model_type,collection_name,disk,created_at');
            })
            ->where('company_id', app('company_id'))
            ->findOrFail($id);
    }
}
