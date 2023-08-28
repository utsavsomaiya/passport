<?php

declare(strict_types=1);

use Facades\App\Enums\Permission as EnumsPermission;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

test('it cannot perform any action without any proper permission', function ($data): void {
    [2 => $token] = frontendApiLoginWithUser('Access Manager');

    $response = $this->withToken($token)->{$data['method'] . 'Json'}($data['route']);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
})->with(getRoutes([
    'users',
    'roles',
    'currencies',
    'hierarchies',
    'price_books',
    'templates',
    'attributes',
    'products',
    'bundle_product_components' => [
        'parameters' => ['parentProductId' => fake()->uuid(), 'id' => fake()->uuid()],
        'rename' => [
            'create' => 'add',
        ],
    ],
    'hierarchy_product' => [
        'parameters' => ['hierarchyId' => fake()->uuid(), 'productId' => fake()->uuid()],
        'rename' => [
            'create' => 'create_or_update',
            'update' => null,
        ],
    ],
]));

test('it can check the request has put the roles and permissions into the cache', function (): void {
    [0 => $user, 2 => $token] = frontendApiLoginWithPermissions(
        ['title' => EnumsPermission::ability('fetch', 'users')]
    );

    $this->withToken($token)->getJson(route('api.users.fetch'));

    ['roles' => $roles, 'permissions' => $permissions] = Cache::get('roles_and_permissions_of_user_' . $user->id);

    expect($roles)->toHaveKey('Access Manager')->toBeIterable();

    expect($permissions)->toMatchArray(['fetch-users']);
});
