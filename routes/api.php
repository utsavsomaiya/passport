<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\GenerateTokenController;
use App\Http\Controllers\Api\HierarchyController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Middleware\AddCompanyIdInServiceContainer;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('api.')->group(function () {
    Route::post('companies/fetch', CompanyController::class)->name('companies.fetch');

    Route::middleware(ThrottleRequests::with(5, 1))
        ->post('generate-token', [GenerateTokenController::class, 'generateToken'])
        ->name('generate_token');

    Route::middleware(['auth:sanctum', AddCompanyIdInServiceContainer::class])->group(function (): void {
        Route::controller(LocaleController::class)
            ->name('locales.')
            ->prefix('locales')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::LOCALES->can('fetch')))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->middleware(Authorize::using(PermissionEnum::LOCALES->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::LOCALES->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::LOCALES->can('update')))
                    ->name('update');
            });

        Route::controller(CurrencyController::class)
            ->name('currencies.')
            ->prefix('currencies')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::CURRENCIES->can('fetch')))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->middleware(Authorize::using(PermissionEnum::CURRENCIES->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::CURRENCIES->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::CURRENCIES->can('update')))
                    ->name('update');
            });

        Route::controller(HierarchyController::class)
            ->name('hierarchies.')
            ->prefix('hierarchies')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::HIERARCHIES->can('fetch')))
                    ->name('fetch');

                Route::post('create/{parent?}', 'create')
                    ->middleware(Authorize::using(PermissionEnum::HIERARCHIES->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::HIERARCHIES->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::HIERARCHIES->can('update')))
                    ->name('update');
            });
    });
});
