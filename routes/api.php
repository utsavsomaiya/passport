<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\BundleProductController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\GenerateTokenController;
use App\Http\Controllers\Api\HierarchyController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PriceBookController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductMediaController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\RoleUserController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::middleware(ThrottleRequestsWithRedis::using('auth'))->group(function (): void {
        Route::post('generate-token', [GenerateTokenController::class, 'generateToken'])->name('generate_token');
        Route::post('forgot-password', ForgotPasswordController::class)->name('forgot_password');
        Route::post('reset-password', ResetPasswordController::class)->name('reset_password');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::controller(CompanyController::class)->name('companies.')->prefix('companies')->group(function (): void {
            Route::get('fetch-current-user-companies', 'fetchCompanies')->name('fetch_current_user_companies');
            Route::post('set', 'setCompany')->name('set');
        });

        Route::middleware('check.company')->group(function (): void {
            Route::controller(UserController::class)->name('users.')->prefix('users')->group(function (): void {
                Route::get('fetch', 'fetch')->can('fetch-users')->name('fetch');
                Route::post('create', 'create')->can('create-user')->name('create');
                Route::delete('{id}/delete', 'delete')->can('delete-user')->name('delete');
                Route::post('{id}/restore', 'restore')->can('delete-user')->name('restore');
                Route::post('{id}/update', 'update')->can('update-user')->name('update');
                Route::post('change-password', 'changePassword')->name('change_password');
            });

            Route::controller(RoleController::class)->name('roles.')->prefix('roles')->group(function (): void {
                Route::get('fetch', 'fetch')->can('fetch-roles')->name('fetch');
                Route::post('create', 'create')->can('create-role')->name('create');
                Route::delete('{id}/delete', 'delete')->can('delete-role')->name('delete');
                Route::post('{id}/update', 'update')->can('update-role')->name('update');
            });

            Route::controller(RoleUserController::class)->name('role_user.')->group(function (): void {
                Route::post('assign-roles', 'assignRoles')->can('manage-user-roles')->name('assign_roles');
                Route::post('dissociate-roles', 'dissociateRoles')->can('manage-user-roles')->name('dissociate_roles');
            });

            Route::name('permissions.')->prefix('permissions')->group(function (): void {
                Route::get('fetch', [PermissionController::class, 'fetch'])->can('manage-role-permissions')->name('fetch');
                Route::post('give-permissions', [RolePermissionController::class, 'givePermissions'])->can('manage-role-permissions')->name('give');
                Route::post('revoke-permissions', [RolePermissionController::class, 'revokePermissions'])->can('manage-role-permissions')->name('revoke');
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
                Route::get('fetch', 'fetch')->can('fetch-attributes')->name('fetch');
                Route::post('create', 'create')->can('create-attribute')->name('create');
                Route::delete('{id}/delete', 'delete')->can('delete-attribute')->name('delete');
                Route::post('{id}/update', 'update')->can('update-attribute')->name('update');
            });

            Route::controller(ProductController::class)->name('products.')->prefix('products')->group(function (): void {
                Route::get('fetch', 'fetch')->can('fetch-products')->name('fetch');
                Route::post('create', 'create')->can('create-product')->name('create');
                Route::delete('{id}/delete', 'delete')->can('delete-product')->name('delete');
                Route::post('{id}/update', 'update')->can('update-product')->name('update');
            });

            Route::controller(ProductMediaController::class)->name('product_media.')->prefix('product-media')->group(function (): void {
                Route::get('{productId}/fetch', 'fetch')->can('manage-product-images')->name('fetch');
                Route::post('{productId}/create', 'create')->can('manage-product-images')->name('create');
                Route::delete('{productId}/{id}/delete', 'delete')->can('manage-product-images')->name('delete');
            });

            Route::controller(BundleProductController::class)->name('bundle_product')->prefix('bundle-product')->group(function (): void {
                Route::post('{productId}/create', 'create')->can('manage-bundle-product')->name('create');
                Route::post('{productId}/delete', 'delete')->can('manage-bundle-product')->name('delete');
            });
        });
    });
});
