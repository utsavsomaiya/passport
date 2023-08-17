<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BundleProductComponent
 *
 * @property string $id
 * @property string $parent_product_id
 * @property string $child_product_id
 * @property int $quantity
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Product $childProduct
 * @method static \Database\Factories\BundleProductComponentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent query()
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereChildProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereParentProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BundleProductComponent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BundleProductComponent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['parent_product_id', 'child_product_id', 'quantity', 'sort_order'];

    public function childProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }
}
