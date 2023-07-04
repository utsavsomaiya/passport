<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Role extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'guard_name'];

    /**
     * Find a role by its id (and optionally guardName).
     *
     * @throws InvalidArgumentException
     */
    public static function findById(int|string $id, ?string $guardName = null): Role
    {
        $guardName ??= config('auth.defaults.guard');

        $role = static::findByParam([(new self())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (! $role instanceof Role) {
            throw new InvalidArgumentException(sprintf('There is no role with id `%s`.', $id));
        }

        return $role;
    }

    /**
     * Find a role by its name and guard name.
     *
     * @throws InvalidArgumentException
     */
    public static function findByName(string $name, ?string $guardName = null): Role
    {
        $guardName ??= config('auth.defaults.guard');

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role instanceof Role) {
            throw new InvalidArgumentException(sprintf('There is no role named `%s`.', $name));
        }

        return $role;
    }

    /**
     * Finds a role based on an array of parameters.
     *
     * @param  array<string, string>  $params
     */
    protected static function findByParam(array $params = []): ?Role
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }
}
