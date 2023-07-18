<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\GenerateTokenController;
use App\Http\Controllers\Api\HierarchyController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\PriceBookController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoleUserController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\UserController;
use App\Permission;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('companies/fetch', CompanyController::class)->name('companies.fetch');

    Route::middleware(ThrottleRequests::with(5, 1))
        ->post('generate-token', [GenerateTokenController::class, 'generateToken'])
        ->name('generate_token');

    Route::middleware(['auth:sanctum', 'set.company'])->group(function (): void {
        Route::controller(UserController::class)->name('users.')->prefix('users')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'users'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'users'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'users'))
                ->name('delete');

            Route::post('{id}/restore', 'restore')
                ->can(Permission::ability('delete', 'users'))
                ->name('restore');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'users'))
                ->name('update');
        });

        Route::controller(RoleController::class)->name('roles.')->prefix('roles')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'roles'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'roles'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'roles'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'roles'))
                ->name('update');
        });

        Route::controller(RoleUserController::class)->name('role_user.')->group(function (): void {
            Route::post('assign-roles', 'assignRoles')
                ->can('assign-user-roles')
                ->name('assign_roles');

            Route::post('dissociate-roles', 'dissociateRoles')
                ->can('dissociate-user-roles')
                ->name('dissociate_roles');
        });

        Route::controller(LocaleController::class)->name('locales.')->prefix('locales')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'locales'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'locales'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'locales'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'locales'))
                ->name('update');
        });

        Route::controller(CurrencyController::class)->name('currencies.')->prefix('currencies')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'currencies'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'currencies'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'currencies'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'currencies'))
                ->name('update');
        });

        Route::controller(HierarchyController::class)->name('hierarchies.')->prefix('hierarchies')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'hierarchies'))
                ->name('fetch');

            Route::post('create/{parent?}', 'create')
                ->can(Permission::ability('create', 'hierarchies'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'hierarchies'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'hierarchies'))
                ->name('update');
        });

        Route::controller(PriceBookController::class)->name('price_books.')->prefix('price-books')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'price-books'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'price-books'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'price-books'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'price-books'))
                ->name('update');
        });

        Route::controller(TemplateController::class)->name('templates.')->prefix('templates')->group(function (): void {
            Route::get('fetch', 'fetch')
                ->can(Permission::ability('fetch', 'templates'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'templates'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'templates'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'templates'))
                ->name('update');
        });

        Route::controller(AttributeController::class)->name('attributes.')->prefix('attributes')->group(function (): void {
            Route::get('fetch/{templateId?}', 'fetch')
                ->can(Permission::ability('fetch', 'attributes'))
                ->name('fetch');

            Route::post('create', 'create')
                ->can(Permission::ability('create', 'attributes'))
                ->name('create');

            Route::delete('{id}/delete', 'delete')
                ->can(Permission::ability('delete', 'attributes'))
                ->name('delete');

            Route::post('{id}/update', 'update')
                ->can(Permission::ability('update', 'attributes'))
                ->name('update');
        });
    });
});
