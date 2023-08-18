<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\BundleProductComponent;
use Closure;
use DB;
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
            ->select('bundle_product_components.id as id', 'parent_product_id', 'child_product_id', 'quantity', 'sort_order')
            ->with('childProduct', function ($query): void {
                $query->select(resolve(ProductQueries::class)->selectedColumns());
            })
            ->jsonPaginate();
    }

    public function create(Request $request, string $parentProductId): void
    {
        $product = resolve(ProductQueries::class)->fetchProduct($parentProductId);

        $bundleProductComponents = [];

        $request->collect('bundle_product_components')->each(function ($bundleProductComponent) use (&$bundleProductComponents, $product): void {
            $bundleProductComponents[] = [
                'parent_product_id' => $product->id,
                'child_product_id' => $bundleProductComponent['id'],
                'quantity' => $bundleProductComponent['quantity'],
                'sort_order' => $bundleProductComponent['sort_order'] ?? null,
            ];
        });

        DB::transaction(function () use ($bundleProductComponents, $product): void {
            BundleProductComponent::query()->upsert($bundleProductComponents, ['parent_product_id', 'child_product_id']);

            if (! $product->is_bundle) {
                $product->is_bundle = true;
                $product->save();
            }
        });
    }

    public function delete(string $id): void
    {
        $bundleProductComponent = BundleProductComponent::findOrFail($id);
        $bundleProductComponentParentProductId = $bundleProductComponent->parent_product_id;

        DB::transaction(function () use ($bundleProductComponent, $bundleProductComponentParentProductId): void {
            $bundleProductComponent->delete();

            $remainingBundleCount = BundleProductComponent::where('parent_product_id', $bundleProductComponentParentProductId)->count();

            if ($remainingBundleCount === 0) {
                resolve(ProductQueries::class)->unbundleProduct($bundleProductComponentParentProductId);
            }
        });
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    public function update(array $validatedData, string $bundleId): void
    {
        $productBundle = BundleProductComponent::query()->findOrFail($bundleId);

        $productBundle->update($validatedData);
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
