<?php

declare(strict_types=1);

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\PermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
        PermissionEnum::getFeatureGates()->each(function ($gate): void {
            Gate::define($gate, function (User $user) use ($gate) {
                $user->load('roles');
                $user->roles->load('permissions');

                $userPermissions = $user->roles->map(function ($role) use ($gate): bool {
                    if (str($role->name)->title->value === 'Super Admin') {
                        return true;
                    }

                    $permissions = $role->permissions()->pluck('title')->toArray();

                    return in_array($gate, $permissions);
                });

                if ($userPermissions->isNotEmpty()) {
                    return $userPermissions->doesntContain(false);
                }

                return false;
            });
        });
    }
}
