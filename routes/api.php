<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
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
        Route::controller(UserController::class)
            ->name('users.')
            ->prefix('users')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->can(PermissionEnum::USERS->can('fetch'))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->can(PermissionEnum::USERS->can('create'))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->can(PermissionEnum::USERS->can('delete'))
                    ->name('delete');

                Route::post('{id}/restore', 'restore')
                    ->can(PermissionEnum::USERS->can('delete'))
                    ->name('restore');

                Route::post('{id}/update', 'update')
                    ->can(PermissionEnum::USERS->can('update'))
                    ->name('update');
            });

        Route::controller(RoleController::class)
            ->name('roles.')
            ->prefix('roles')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->can(PermissionEnum::ROLES->can('fetch'))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->can(PermissionEnum::ROLES->can('create'))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->can(PermissionEnum::ROLES->can('delete'))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->can(PermissionEnum::ROLES->can('update'))
                    ->name('update');
            });

        Route::controller(RoleUserController::class)
            ->name('role_user.')
            ->group(function (): void {
                Route::post('assign-roles', 'assignRoles')
                    ->can(PermissionEnum::USER_ROLE->can('attach'))
                    ->name('assign_roles');

                Route::post('dissociate-roles', 'dissociateRoles')
                    ->can(PermissionEnum::USER_ROLE->can('detach'))
                    ->name('dissociate_roles');
            });



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

        Route::controller(PriceBookController::class)
            ->name('price_books.')
            ->prefix('price-books')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::PRICE_BOOKS->can('fetch')))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->middleware(Authorize::using(PermissionEnum::PRICE_BOOKS->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::PRICE_BOOKS->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::PRICE_BOOKS->can('update')))
                    ->name('update');
            });

        Route::controller(TemplateController::class)
            ->name('templates.')
            ->prefix('templates')
            ->group(function (): void {
                Route::get('fetch', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::TEMPLATES->can('fetch')))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->middleware(Authorize::using(PermissionEnum::TEMPLATES->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::TEMPLATES->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::TEMPLATES->can('update')))
                    ->name('update');
            });

        Route::controller(AttributeController::class)
            ->name('attributes.')
            ->prefix('attributes')
            ->group(function (): void {
                Route::get('fetch/{templateId?}', 'fetch')
                    ->middleware(Authorize::using(PermissionEnum::ATTRIBUTES->can('fetch')))
                    ->name('fetch');

                Route::post('create', 'create')
                    ->middleware(Authorize::using(PermissionEnum::ATTRIBUTES->can('create')))
                    ->name('create');

                Route::delete('{id}/delete', 'delete')
                    ->middleware(Authorize::using(PermissionEnum::ATTRIBUTES->can('delete')))
                    ->name('delete');

                Route::post('{id}/update', 'update')
                    ->middleware(Authorize::using(PermissionEnum::ATTRIBUTES->can('update')))
                    ->name('update');
            });
    });
});
