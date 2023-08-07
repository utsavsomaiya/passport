<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBundle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['parent_product_id', 'child_product_id', 'quantity', 'sort_order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'child_product_id')->with('productBundles');
    }
}
