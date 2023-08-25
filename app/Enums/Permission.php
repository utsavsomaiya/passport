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
         * For altering an array of actions, supply the array alongside an array of key-value pairs.
         * If a '' value is passed, the corresponding action will be removed from the array.
         *
         * @var array<string|int, mixed>
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
            'product-bundles',
            'bundle-product-components' => [
                'create' => 'add',
            ],
            'hierarchy-products' => [
                'create' => 'create-or-update',
                'update' => null,
            ],
        ],

        /**
         * @var array<int, string>
         */
        protected array $permissions = [
            'manage-user-roles',
            'manage-role-permissions',
            'manage-product-images',
        ]
    ) {

    }

    /**
     * @return array<string, string>
     */
    public function listOfPermissions(): array
    {
        return $this->getFeatureGates()->mapWithKeys(fn (string $name): array => [
            $name => Str::of($name)->replaceFirst('-', ' ')->title()->value(),
        ])->toArray();
    }

    public function ability(string $action, string $for): string
    {
        return $this->generateAction($action, Str::singular($for));
    }

    public function getFeatureGates(): Collection
    {
        return collect($this->modules)
            ->map(fn (string|array $name, string|int $key): array => is_array($name) ? $this->generateCrud(Str::singular((string) $key), $name) : $this->generateCrud(Str::singular($name)))
            ->push($this->permissions)
            ->flatten();
    }

    /**
     * @param  array<string, string>  $renameActions
     * @return array<int, string>
     */
    private function generateCrud(string $for, array $renameActions = []): array
    {
        return collect(['fetch', 'create', 'update', 'delete'])
            ->map(fn (string $action): mixed => $renameActions !== [] && array_key_exists($action, $renameActions) ? $renameActions[$action] : $action)
            ->filter(fn (?string $action): bool => ! blank($action))
            ->map(fn (string $action): string => $this->generateAction($action, $for))
            ->toArray();
    }

    private function generateAction(string $action, string $for): string
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
