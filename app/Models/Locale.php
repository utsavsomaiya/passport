<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Locale
 *
 * @property string $id
 * @property string $company_id
 * @property string $name
 * @property string $code
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Company $company
 * @method static \Database\Factories\LocaleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Locale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locale whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Locale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['company_id', 'name', 'code', 'status'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
