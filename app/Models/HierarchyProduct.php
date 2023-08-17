<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\HierarchyProduct
 *
 * @property string $hierarchy_id
 * @property string $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct whereHierarchyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HierarchyProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HierarchyProduct extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['hierarchy_id', 'product_id'];
}
