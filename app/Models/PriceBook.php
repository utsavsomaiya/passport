<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PriceBook
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Company $company
 * @method static \Database\Factories\PriceBookFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceBook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceBook extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['company_id', 'name', 'description'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
