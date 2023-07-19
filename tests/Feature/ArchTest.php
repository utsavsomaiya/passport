<?php

test('laravel model can use only on base model')
    ->expect('App\Models\Model')
    ->toUse('Illuminate\Database\Eloquent\Model');

test('model can extends base model not laravel default model')
    ->expect('App\Models')
    ->not->toUse('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\Model');

test('model only used in the query class not in controller')
    ->expect('App\Models')
    ->toOnlyBeUsedIn('App\Queries')
    ->ignoring([
        'Database\Seeders',
        'Database\Factories',
        'App\Providers',
        'App\Models'
    ]);

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
