<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
