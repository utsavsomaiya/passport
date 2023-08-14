<?php

declare(strict_types=1);

use App\Models\Model;
use Illuminate\Database\Eloquent\Model as BaseModel;

test('laravel model can use only on base model')
    ->expect(Model::class)
    ->toUse(BaseModel::class);

test('it can use strict type on whole applications')
    ->expect('App')
    ->toUseStrictTypes();

test('model can extends base model not laravel default model')
    ->expect('App\Models')
    ->not->toUse(BaseModel::class)
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

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

test('it can check the controllers have `Controller` suffix')
    ->expect('App\\Http\\Controllers\\')
    ->toHaveSuffix('Controller');

test('it can check the jobs have `Job` suffix')
    ->expect('App\Jobs')
    ->toHaveSuffix('Job');

test('it can check the request have `Request` suffix')
    ->expect('App\\Http\\Requests\\')
    ->toHaveSuffix('Request');

test('it can check the resource have `Resource` suffix')
    ->expect('App\\Http\\Resources\\')
    ->toHaveSuffix('Resource');
