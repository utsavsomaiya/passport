<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Attribute extends Model
{
    use HasFactory;
    use HasUuids;
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
