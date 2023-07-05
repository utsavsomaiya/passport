<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Role;
use App\Models\User;
use BackedEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasRoles
{
    public static function bootHasRoles(): void
    {
        static::deleting(function (User $model): void {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
        });
    }

    /**
     * Assign the given role to the model.
     *
     * @param  array<int, string>|string|int|Role|Collection  ...$roles
     * @return $this
     */
    public function assignRole(array|string|int|Role|Collection $roles = [])
    {
        if ($roles instanceof Role) {
            $roles = $roles->getKey();
            $roles = Arr::wrap($roles);
        }

        $roles = $this->collectRoles($roles);

        $model = $this->getModel();

        if ($model->exists) {
            $currentRoles = $this->roles->map(fn ($role) => $role->getKey())->toArray();

            $roles = array_diff($roles, $currentRoles);

            if ($roles !== []) {

                $this->roles()->attach(array_combine(
                    $roles,
                    array_map(fn (): array => ['created_at' => now(), 'updated_at' => now()], $roles)
                ));

                $model->unsetRelation('roles');
            }
        } else {
            $class = $model::class;

            $class::saved(
                function ($object) use ($roles, $model): void {
                    if ($model->getKey() !== $object->getKey()) {
                        return;
                    }

                    $model->roles()->attach(array_combine(
                        $roles,
                        array_map(fn (): array => ['created_at' => now(), 'updated_at' => now()], $roles)
                    ));
                    $model->unsetRelation('roles');
                }
            );
        }

        return $this;
    }

    /**
     * Returns roles ids as array keys
     */
    // @phpstan-ignore-next-line
    private function collectRoles($roles = []): array
    {
        // @phpstan-ignore-next-line
        return collect($roles)
            ->flatten()
            ->reduce(function ($carry, $role) {
                if (empty($role)) {
                    return $carry;
                }

                if (Str::isUuid($role)) {
                    $carry[] = $role;

                    return $carry;
                }

                if ($role instanceof Role) {
                    $carry[] = $role->getKey();

                    return $carry;
                }

                $role = $this->getStoredRole($role);

                if (! $role instanceof Role) {
                    return $carry;
                }

                $carry[] = $role->getKey();

                return $carry;
            }, []);
    }

    protected function getStoredRole(BackedEnum|Role|string $role): Role
    {
        if ($role instanceof BackedEnum) {
            $role = $role->value;
        }

        if ($role instanceof Role) {
            return $role;
        }

        /** @var string $role */
        if (Str::isUuid($role)) {
            return Role::findById($role, config('auth.defaults.guard'));
        }

        return Role::findByName($role, config('auth.defaults.guard'));
    }

    /**
     * Remove all current roles and set the given ones.
     *
     * @return $this
     */
    // @phpstan-ignore-next-line
    public function syncRoles(array $roles = [])
    {
        if ($this->getModel()->exists) {
            $this->collectRoles($roles);
            $this->roles()->detach();
            $this->setRelation('roles', collect());
        }

        return $this->assignRole($roles);
    }

    /**
     * Revoke the given role from the model.
     *
     * @return $this
     */
    public function removeRole(string|Role $role)
    {
        $this->roles()->detach($this->getStoredRole($role));

        $this->unsetRelation('roles');

        return $this;
    }
}
