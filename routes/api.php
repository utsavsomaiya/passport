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
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('companies/fetch', CompanyController::class)->name('companies.fetch');

    Route::middleware(ThrottleRequests::with(5, 1))
        ->post('generate-token', [GenerateTokenController::class, 'generateToken'])
        ->name('generate_token');

    Route::middleware(['auth:sanctum', 'set.company'])->group(function (): void {
        Route::controller(UserController::class)->name('users.')->prefix('users')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-users')->name('fetch');
            Route::get('fetch/{roleId}', 'fetchByRole')->can('fetch-users')->name('fetch_by_role');
            Route::post('create', 'create')->can('create-user')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-user')->name('delete');
            Route::post('{id}/restore', 'restore')->can('delete-user')->name('restore');
            Route::post('{id}/update', 'update')->can('update-user')->name('update');
        });

        Route::controller(RoleController::class)->name('roles.')->prefix('roles')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-roles')->name('fetch');
            Route::post('create', 'create')->can('create-role')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-role')->name('delete');
            Route::post('{id}/update', 'update')->can('update-role')->name('update');
        });

        Route::controller(RoleUserController::class)->name('role_user.')->group(function (): void {
            Route::post('assign-roles', 'assignRoles')->can('assign-user-roles')->name('assign_roles');
            Route::post('dissociate-roles', 'dissociateRoles')->can('dissociate-user-roles')->name('dissociate_roles');
        });

        Route::controller(LocaleController::class)->name('locales.')->prefix('locales')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-locales')->name('fetch');
            Route::post('create', 'create')->can('create-locale')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-locale')->name('delete');
            Route::post('{id}/update', 'update')->can('update-locale')->name('update');
        });

        Route::controller(CurrencyController::class)->name('currencies.')->prefix('currencies')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-currencies')->name('fetch');
            Route::post('create', 'create')->can('create-currency')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-currency')->name('delete');
            Route::post('{id}/update', 'update')->can('update-currency')->name('update');
        });

        Route::controller(HierarchyController::class)->name('hierarchies.')->prefix('hierarchies')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-hierarchies')->name('fetch');
            Route::post('create/{parent?}', 'create')->can('create-hierarchy')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-hierarchy')->name('delete');
            Route::post('{id}/update', 'update')->can('update-hierarchy')->name('update');
        });

        Route::controller(PriceBookController::class)->name('price_books.')->prefix('price-books')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-price-books')->name('fetch');
            Route::post('create', 'create')->can('create-price-book')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-price-book')->name('delete');
            Route::post('{id}/update', 'update')->can('update-price-book')->name('update');
        });

        Route::controller(TemplateController::class)->name('templates.')->prefix('templates')->group(function (): void {
            Route::get('fetch', 'fetch')->can('fetch-templates')->name('fetch');
            Route::post('create', 'create')->can('create-template')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-template')->name('delete');
            Route::post('{id}/update', 'update')->can('update-template')->name('update');
        });

        Route::controller(AttributeController::class)->name('attributes.')->prefix('attributes')->group(function (): void {
            Route::get('fetch/{templateId?}', 'fetch')->can('fetch-attributes')->name('fetch');
            Route::post('create', 'create')->can('create-attribute')->name('create');
            Route::delete('{id}/delete', 'delete')->can('delete-attribute')->name('delete');
            Route::post('{id}/update', 'update')->can('update-attribute')->name('update');
        });
    });
});
