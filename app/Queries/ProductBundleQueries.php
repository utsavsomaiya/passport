<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductBundleQueries extends GlobalQueries
{
    public function listQuery(Request $request, string $parentProductId): LengthAwarePaginator
    {
        return QueryBuilder::for(ProductBundle::class, $request)
            ->defaultSort('-sort_order')
            ->allowedSorts([
                'quantity',
                'sort_order',
                AllowedSort::callback('name', $this->sortingWithRelationShips('childProduct')),
            ])
            ->allowedFilters([
                $this->filterWithRelationship('name', 'childProduct'),
                $this->filterWithRelationship('sku', 'childProduct'),
                'quantity',
                'sort_order',
            ])
            ->where('parent_product_id', $parentProductId)
            ->select('id', 'parent_product_id', 'child_product_id', 'sort_order', 'quantity')
            ->with('childProduct', function ($query): void {
                $query->select(resolve(ProductQueries::class)->selectedColumns());
            })
            ->jsonPaginate();
    }

    public function create(Request $request, string $parentProductId): void
    {
        $product = resolve(ProductQueries::class)->getProductIdByBundle($parentProductId);

        $bundleProducts = [];

        $request->collect('bundle_products')->each(function ($bundleProduct) use (&$bundleProducts, $product): void {
            $bundleProducts[] = [
                'parent_product_id' => $product->id,
                'child_product_id' => $bundleProduct['id'],
                'quantity' => $bundleProduct['quantity'],
                'sort_order' => $bundleProduct['sort_order'] ?? null,
            ];
        });

        DB::transaction(function () use ($bundleProducts, $product): void {
            ProductBundle::query()->upsert($bundleProducts, ['parent_product_id', 'child_product_id']);

            if (! $product->is_bundle) {
                $product->is_bundle = true;
                $product->save();
            }
        });
    }

    public function delete(string $bundleId): void
    {
        $productBundle = ProductBundle::findOrFail($bundleId);
        $productBundleParentProductId = $productBundle->parent_product_id;

        DB::transaction(function () use ($productBundle, $productBundleParentProductId): void {
            $productBundle->delete();

            $remainingBundleCount = ProductBundle::where('parent_product_id', $productBundleParentProductId)->count();

            if ($remainingBundleCount === 0) {
                Product::where('id', $productBundleParentProductId)->update(['is_bundle' => false]);
            }
        });
    }

    /**
     * @param  array<string, string>  $validatedData
     */
    public function update(array $validatedData, string $bundleId): void
    {
        $productBundle = ProductBundle::query()->findOrFail($bundleId);

        $productBundle->update($validatedData);
    }
}
