<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\ProductBundle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->with('childProduct', function ($query) {
                $query->select(resolve(ProductQueries::class)->selectedColumns());
            })
            ->jsonPaginate();
    }

    public function create(Request $request, string $parentProductId): void
    {
        $product = resolve(ProductQueries::class)->getProductIdByBundle($parentProductId, false);

        DB::beginTransaction();

        try {
            $request->collect('bundle_products')->each(function ($bundleProduct) use ($product) {
                ProductBundle::create([
                    'parent_product_id' => $product->id,
                    'child_product_id' => $bundleProduct['id'],
                    'quantity' => $bundleProduct['quantity'],
                    'sort_order' => array_key_exists('sort_order', $bundleProduct) ? $bundleProduct['sort_order'] : null,
                ]);
            });

            $product->is_bundle = true;
            $product->save();

            DB::commit();
        } catch (Exception $exception) {
            Log::error('Create Bundle Product', [
                'Exception Message' => $exception->getMessage(),
                'Stack Trace' => $exception->getTrace(),
            ]);

            DB::rollBack();
        }
    }

    public function delete(string $parentProductId, ?string $childProductId): void
    {
        $product = resolve(ProductQueries::class)->getProductIdByBundleWithCount($parentProductId, true);

        ProductBundle::query()
            ->where('parent_product_id', $parentProductId)
            ->where('child_product_id', $childProductId ?? '')
            ->delete();

        if ($product->product_bundles_count === 1 || $childProductId === null) {
            $product->is_bundle = false;
            $product->save();
        }
    }
}
