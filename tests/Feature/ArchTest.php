<?php

declare(strict_types=1);

use App\Models\Model;

test('laravel model can use only on base model')
    ->expect(Model::class)
    ->toUse(\Illuminate\Database\Eloquent\Model::class);

test('model can extends base model not laravel default model')
    ->expect('App\Models')
    ->not->toUse(\Illuminate\Database\Eloquent\Model::class)
    ->ignoring(Model::class);

test('model only used in the query class not in controller')
    ->expect('App\Models')
    ->toOnlyBeUsedIn('App\Queries')
    ->ignoring([
        'Database\Seeders',
        'Database\Factories',
        'App\Providers',
        'App\Models',
    ]);

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
