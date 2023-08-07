<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\ProductBundle;

class ProductBundleQueries
{
    /**
     * @param  array<int, mixed>  $data
     */
    public function createMany(array $data): void
    {
        ProductBundle::insert($data);
    }

    /**
     * @param  array<int, mixed>  $data
     */
    public function upsert(array $data): void
    {
        ProductBundle::upsert($data, ['parent_product_id', 'child_product_id', 'quantity']);
    }

    public function deleteByParentId(string $parentProductId): void
    {
        ProductBundle::where('parent_product_id', $parentProductId)->delete();
    }
}
