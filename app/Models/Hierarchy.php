<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * App\Models\Hierarchy
 *
 * @property string $id
 * @property string $company_id
 * @property string|null $parent_hierarchy_id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Hierarchy> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\Company $company
 * @method static \Database\Factories\HierarchyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereParentHierarchyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hierarchy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Hierarchy extends Model
{
    use HasFactory;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['company_id', 'parent_hierarchy_id', 'name', 'description', 'slug'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite(); // Ref: https://github.com/spatie/laravel-sluggable#preventing-overwrites
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_hierarchy_id')->with('children');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
