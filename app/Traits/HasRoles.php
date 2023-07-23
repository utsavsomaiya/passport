<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

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
     * @param  array<int, string>  $roles
     * @return $this
     */
    public function assignRole(array $roles)
    {
        $roles = $this->collectRoles($roles);

        $model = $this->getModel();

        if ($model->exists) {
            $currentRoles = $this->roles->map(fn ($role) => $role->getKey())->toArray();

            $roles = array_diff($roles, $currentRoles);

            $prepareRoles = [];

            foreach ($roles as $role) {
                $prepareRoles[$role] = ['created_at' => now(), 'updated_at' => now()];
            }

            if ($prepareRoles !== []) {
                $this->roles()->attach($prepareRoles);

                $model->unsetRelation('roles');
            }
        }

        return $this;
    }

    /**
     * Returns roles ids as array keys
     *
     * @param  array<int, string>  $roles
     * @return array<int, string> $roles
     */
    private function collectRoles(array $roles): array
    {
        return collect($roles)
            ->map(fn (string $role) => $this->getStoredRole($role))
            ->toArray();
    }

    /**
     * Find a role by its name or id.
     *
     * @throws InvalidArgumentException
     */
    private function getStoredRole(string $role): string
    {
        if (Str::isUuid($role)) {
            $roleModel = Role::find($role);

            if ($roleModel?->exists) {
                return $roleModel->getKey();
            }

            throw new InvalidArgumentException(sprintf('There is no role with id `%s`.', $role));
        }

        $roleModel = Role::query()
            ->where('name', $role)
            ->first();

        if ($roleModel?->exists) {
            return $roleModel->getKey();
        }

        throw new InvalidArgumentException(sprintf('There is no role named `%s`.', $role));
    }

    /**
     * Revoke the given role from the model.
     *
     * @param  array<int, string>  $roles
     * @return $this
     */
    public function removeRole(array $roles)
    {
        $this->roles()->detach($this->collectRoles($roles));

        $this->unsetRelation('roles');

        return $this;
    }
}
