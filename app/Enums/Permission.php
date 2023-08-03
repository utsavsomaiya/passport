<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class Permission
{
    // Whenever any change is made to the list of permissions below, we need to fire the
    // queued job to forget all the cache entries of roles and permissions of all users.

    public function __construct(
        /**
         * @var array<int, string>
         */
        protected array $modules = [
            'users',
            'roles',
            'permissions',
            'locales',
            'currencies',
            'hierarchies',
            'price-books',
            'templates',
            'attributes',
            'products',
        ],

        /**
         * @var array<int, string>
         */
        protected array $permissions = [
            'manage-user-roles',
            'manage-role-permissions',
        ]
    ) {

    }

    /**
     * @return array<string, string>
     */
    public static function listOfPermissions(): array
    {
        return self::getFeatureGates()->mapWithKeys(fn (string $name): array => [
            $name => Str::of($name)->replaceFirst('-', ' ')->title()->value(),
        ])->toArray();
    }

    public static function ability(string $action, string $for): string
    {
        return self::generateAction($action, Str::singular($for));
    }

    public static function getFeatureGates(): Collection
    {
        $self = app(self::class);

        return collect($self->modules)
            ->map(fn (string $name): array => self::generateCrud(Str::singular($name)))
            ->push($self->permissions)
            ->flatten();
    }

    /**
     * @return array<int, string>
     */
    private static function generateCrud(string $for): array
    {
        return collect(['fetch', 'create', 'update', 'delete'])
            ->map(fn (string $action): string => self::generateAction($action, $for))
            ->toArray();
    }

    private static function generateAction(string $action, string $for): string
    {
        if ($action === 'fetch') {
            return Str::of($action)
                ->append('-', Str::plural($for))
                ->value();
        }

        return Str::of($action)
            ->append('-', $for)
            ->value();
    }
}
