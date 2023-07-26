<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Permission::getFeatureGates()->each(function (string $gate): void {
            Gate::define($gate, function (User $user) use ($gate) {
                if (Cache::has($user->id)) {
                    ['roles' => $roles, 'permissions' => $permissions] = Cache::get($user->id);

                    if (array_key_exists('Super Admin', $roles)) {
                        return true;
                    }

                    return in_array($gate, $permissions);
                }

                $user->load('roles');
                $user->roles->load('permissions');

                $userPermissions = $user->roles->map(function (Role $role) use ($gate): bool {
                    if (str($role->name)->title->value === 'Super Admin') {
                        return true;
                    }

                    $permissions = $role->permissions()->pluck('title')->toArray();

                    return in_array($gate, $permissions);
                });

                Cache::put($user->id, [
                    'roles' => $user->roles->pluck('id', 'name')->toArray(),
                    'permissions' => $user->roles
                        ->map(fn (Role $role) => $role->permissions()->pluck('title')->toArray())
                        ->flatten()
                        ->toArray(),
                ]);

                if ($userPermissions->isNotEmpty()) {
                    return $userPermissions->contains(true);
                }

                return false;

            });
        });
    }
}
