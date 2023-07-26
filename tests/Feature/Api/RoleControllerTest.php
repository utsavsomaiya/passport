<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch roles', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.roles.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($role = Role::latest()->first())->id)
                                ->where('name', $role->name)
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});

test('it can create role', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.roles.create'), [
        'name' => $name = 'Access Manager',
    ]);

    $response->assertOk()->assertJsonStructure(['success', 'role_id']);

    $this->assertDatabaseHas(Role::class, [
        'name' => $name,
    ]);
});

test('it can delete the role', function (): void {
    $role = Role::factory()->for($this->company)->named('Access Manager')->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.roles.delete', [
        'id' => $role->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertModelMissing($role);
});

test('it cannot delete the role if it assign to the user', function (): void {
    $response = $this->withToken($this->token)->deleteJson(route('api.roles.delete', [
        'id' => Role::min('id'),
    ]));

    $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
});

test('it can update the role', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.roles.update', [
        'id' => Role::min('id'),
    ]), [
        'name' => $name = 'Access Manager',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Role::class, [
        'name' => $name,
    ]);
});

todo('it cannot delete the role because of there are assigned the permissions.. Can we remove this permissions from database?');
