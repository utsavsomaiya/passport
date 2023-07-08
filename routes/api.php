<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Http\Controllers\Api\GenerateTokenController;
use App\Http\Controllers\Api\LocaleController;
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
    Route::middleware(ThrottleRequests::with(5, 1))
        ->post('generate-token', [GenerateTokenController::class, 'generateToken'])
        ->name('generate_token');

    Route::middleware(['auth:sanctum'])->group(function (): void {
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

                Route::put('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::LOCALES->can('update')))
                    ->name('update');
            });
    });
});
