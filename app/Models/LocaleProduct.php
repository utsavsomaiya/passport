<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\LocaleProduct
 *
 * @property string $id
 * @property string $locale_id
 * @property string $product_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\LocaleProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereLocaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocaleProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LocaleProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['locale_id', 'product_id', 'name', 'description'];

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $description): ?string => $description ? html_entity_decode($description) : null,
            set: fn (?string $description): ?string => $description ? htmlentities($description) : null
        );
    }
}
