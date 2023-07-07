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

    public static function getFeatureGates(): Collection
    {
        return collect(self::names())
            ->map(fn ($name) => self::generateCrud(Str::of($name)->lower()->singular()->value()))
            ->flatten();
    }

    public function can(string $action)
    {
        return self::generateAction($action, Str::singular($this->name));
    }

    private static function generateCrud(string $for): array
    {
        $crud = collect(['fetch', 'create', 'update', 'delete']);

        return $crud->map(fn ($action) => self::generateAction($action, $for))->toArray();
    }

    private static function generateAction(string $action, string $for)
    {
        if ($action === 'fetch') {
            return Str::of($action)
                ->title()
                ->append(Str::of($for)->plural()->title())
                ->kebab()
                ->value();
        }

        return Str::of($action)
            ->title()
            ->append(Str::of($for)->title())
            ->kebab()
            ->value();
    }
}
