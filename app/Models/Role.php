<?php

declare(strict_types=1);

namespace App\Models;

use App\Builders\RoleBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Role extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description'];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return RoleBuilder<Role>
     */
    public function newEloquentBuilder($query)
    {
        return new RoleBuilder($query);
    }
}
