<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\RoleUser;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can assign the roles', function (): void {
    $role = Role::factory()->for($this->company)->named('Access Manager')->create();

    $response = $this->withToken($this->token)->postJson(route('api.role_user.assign_roles'), [
        'user' => $this->user->id,
        'roles' => [$role->id],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(RoleUser::class, [
        'role_id' => $role->id,
        'user_id' => $this->user->id,
    ]);
});

test('it can dissociate the roles', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.role_user.dissociate_roles'), [
        'user' => $this->user->id,
        'roles' => [Role::min('id')],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseCount(RoleUser::class, 0);
});
