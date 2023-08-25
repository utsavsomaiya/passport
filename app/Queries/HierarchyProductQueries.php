<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\HierarchyProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HierarchyProductQueries
{
    public function listQuery(Request $request, string $hierarchyId): Model
    {
        return resolve(HierarchyQueries::class)->fetchProductsWithCurated($request, $hierarchyId);
    }

    public function createOrUpdate(Request $request): void
    {
        HierarchyProduct::query()->upsert($request->all(), ['product_id', 'hierarchy_id']);
    }

    public function deleteByHierarchyId(string $hierarchyId): void
    {
        HierarchyProduct::query()->where('hierarchy_id', $hierarchyId)->delete();
    }

    public function delete(string $hierarchyId, string $productId): void
    {
        HierarchyProduct::query()
            ->where('product_id', $productId)
            ->where('hierarchy_id', $hierarchyId)
            ->delete();
    }
}
