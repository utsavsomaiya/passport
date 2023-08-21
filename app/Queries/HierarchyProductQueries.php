<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\HierarchyProduct;

class HierarchyProductQueries
{
    public function deleteByHierarchyId(string $hierarchyId): void
    {
        HierarchyProduct::query()->where('hierarchy_id', $hierarchyId)->delete();
    }
}
