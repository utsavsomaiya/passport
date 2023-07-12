<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Names;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

enum PermissionEnum: int
{
    use Names;

    case USERS = 1;
    case LOCALES = 2;
    case CURRENCIES = 3;
    case HIERARCHIES = 4;
    case PRICE_BOOKS = 5;

    public static function getFeatureGates(): Collection
    {
        return collect(self::names())
            ->map(fn ($name): array => self::generateCrud(Str::of($name)->slug()->singular()->value()))
            ->flatten();
    }

    public function can(string $action): string
    {
        return self::generateAction($action, Str::of($this->name)->slug()->singular()->value());
    }

    /**
     * @return array<int, string>
     */
    private static function generateCrud(string $for): array
    {
        $crud = collect(['fetch', 'create', 'update', 'delete']);

        return $crud->map(fn ($action): string => self::generateAction($action, $for))->toArray();
    }

    private static function generateAction(string $action, string $for): string
    {
        if ($action === 'fetch') {
            // Ref: if action is fetch then the return value is `fetch-locales`
            return Str::of($action)
                ->append('-', Str::of($for)->plural()->title()->value())
                ->slug()
                ->value();
        }

        // Ref: if action is create, update or delete then the return value is `create-locale`, `delete-locale` or `update-locale`
        return Str::of($action)
            ->append('-', Str::of($for)->title()->value())
            ->slug()
            ->value();
    }
}
