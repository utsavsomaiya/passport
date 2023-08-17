<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\BundleProductComponent;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class BundleProductComponentQueries extends GlobalQueries
{
    public function listQuery(Request $request, string $parentProductId): LengthAwarePaginator
    {
        return QueryBuilder::for(BundleProductComponent::class, $request)
            ->defaultSort('-sort_order')
            ->allowedSorts(['quantity', 'sort_order', AllowedSort::callback('name', $this->sortingWithRelationShips())])
            ->allowedFilters([
                $this->filterWithRelationship('name', 'childProduct'),
                $this->filterWithRelationship('sku', 'childProduct'),
                'quantity',
                'sort_order',
            ])
            ->where('parent_product_id', $parentProductId)
            ->with('childProduct', function ($query): void {
                $query->select(resolve(ProductQueries::class)->selectedColumns());
            })
            ->jsonPaginate();
    }

    private function sortingWithRelationShips(): Closure
    {
        return function (Builder $query, bool $descending, string $property): void {
            $direction = $descending ? 'DESC' : 'ASC';
            $query->join('products', 'products.id', '=', 'bundle_product_components.child_product_id')
                ->orderBy($property, $direction);
        };
    }
}
