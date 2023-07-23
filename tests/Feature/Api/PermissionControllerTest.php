<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Permission as AppPermission;
use Illuminate\Support\Str;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch the static permission list', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.permissions.fetch'));

    $permissions = AppPermission::getFeatureGates()->mapWithKeys(fn ($name, $key): array => [
        $name => Str::of($name)->replaceFirst('-', ' ')->title()->value(),
    ])->toArray();

    $response->assertOk()
        ->assertJson(['permissions' => $permissions]);
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

    $permissions = Permission::factory(3)->for($role)->sequence(
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
