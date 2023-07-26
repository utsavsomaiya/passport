<?php

declare(strict_types=1);

use App\Models\Permission;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch the static permission list', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.permissions.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'permissions',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->where('fetch-users', 'Fetch Users')
                        ->where('create-user', 'Create User')
                        ->where('create-price-book', 'Create Price-Book')
                        ->etc()
                )
        );
});

test('it can give the permissions', function (): void {
    $role = $this->user->roles->first();

    Permission::factory()->for($role)->create();

    $response = $this->withToken($this->token)->postJson(route('api.permissions.give'), [
        'role' => $role->id,
        'permissions' => ['create-user'],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Permission::class, [
        'role_id' => $role->id,
        'title' => 'create-user',
    ]);
});

test('it can revoke the permissions', function (): void {
    $role = $this->user->roles->first();

    Permission::factory(3)->for($role)->sequence(
        ['title' => 'create-user'],
        ['title' => 'delete-user'],
        ['title' => 'fetch-users']
    )->create();

    $response = $this->withToken($this->token)->postJson(route('api.permissions.revoke'), [
        'role' => $role->id,
        'permissions' => ['create-user', 'delete-user', 'fetch-users'],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    expect(Permission::first())->toBeNull();
});
