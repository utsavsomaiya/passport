<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * App\Models\Attribute
 *
 * @property string $id
 * @property string $template_id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property FieldType $field_type     1. Radio Button (switch, boolean)
 *     2. Input (type=number, step=any, float)
 *     3. Input (type=number, integer)
 *     4. Input (type=text, string)
 *     5. Input (type=date, date)
 *     6. Select (multiple=false, string)
 *     7. Select (multiple=true, array)
 * @property string|null $default_value
 * @property string|null $from
 * @property string|null $to
 * @property array|null $options If field type is select or list then user gives its options
 * @property bool $is_required
 * @property bool $status
 * @property int|null $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Template $template
 * @method static \Database\Factories\AttributeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attribute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Attribute extends Model
{
    use HasFactory;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_id',
        'name',
        'description',
        'slug',
        'field_type',
        'default_value',
        'from',
        'to',
        'options',
        'is_required',
        'status',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'field_type' => FieldType::class,
        'options' => 'array',
        'is_required' => 'boolean',
        'status' => 'boolean',
    ];

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

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
