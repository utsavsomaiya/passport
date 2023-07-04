<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Role;
use BackedEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasRoles
{
    /**
     * Assign the given role to the model.
     *
     * @param  array<int, string>|string|int|Role|Collection  ...$roles
     * @return $this
     */
    public function assignRole(array|string|int|Role|Collection $roles = [])
    {
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

                $role = $this->getStoredRole($role);

                if (! $role instanceof Role) {
                    return $carry;
                }

                $carry[] = $role->getKey();

                return $carry;
            }, []);
    }

    protected function getStoredRole(BackedEnum|string $role): Role
    {
        if ($role instanceof BackedEnum) {
            $role = $role->value;
        }

        /** @var string $role */
        if (Str::isUuid($role)) {
            return Role::findById($role, config('auth.defaults.guard'));
        }

        return Role::findByName($role, config('auth.defaults.guard'));
    }
}
