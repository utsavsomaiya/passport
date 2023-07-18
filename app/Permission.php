<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final readonly class Permission
{
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
        ],

        /**
         * @var array<int, string>
         */
        protected array $permissions = [
            'assign-user-roles',
            'dissociate-user-roles',
        ]
    ) {

    }

    public static function ability(string $action, string $for): string
    {
        return self::generateAction($action, Str::singular($for));
    }

    public static function getFeatureGates(): Collection
    {
        $self = app(self::class);

        return collect($self->modules)
            ->map(fn ($name): array => self::generateCrud(Str::singular($name)))
            ->push($self->permissions)
            ->flatten();
    }

    /**
     * @return array<int, string>
     */
    private static function generateCrud(string $for): array
    {
        return collect(['fetch', 'create', 'update', 'delete'])
            ->map(fn ($action): string => self::generateAction($action, $for))
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
