<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

test('it can assign role using name', function (): void {
    $user = User::factory()->create();

    $role = Role::factory()->named($name = 'Developer')->create();

    $user->assignRole($name);

    $this->assertDatabaseHas($user->roles()->getTable(), [
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);
});

test('it can remove the roles', function (): void {
    $user = User::factory()->create();

    $user->roles()->attach($role = Role::factory()->create());

    $user->removeRole($role);

    $this->assertDatabaseCount($user->roles()->getTable(), 0);
});

test('it can assign multiple roles', function (): void {
    $user = User::factory()->create();

    $roles = Role::factory(2)->create();

    $user->assignRole($roles);

    $this->assertDatabaseCount($user->roles()->getTable(), 2);
});

test('it can assign multiple roles using name also', function (): void {
    $user = User::factory()->create();

    $roles = Role::factory(2)->create();

    $user->assignRole($roles->pluck('name'));

    $this->assertDatabaseCount($user->roles()->getTable(), 2);
});
