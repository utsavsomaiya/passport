<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch users', function ($role): void {
    User::factory(2)->create();

    $response = $this->withToken($this->token)->getJson(route('api.users.fetch'), [
        'role_id' => $role,
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($user = User::latest()->first())->id)
                                ->where('first_name', $user->first_name)
                                ->where('last_name', $user->last_name)
                                ->where('roles', $user->roles->pluck('name')->toArray())
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
})->with([null, fn () => Role::min('id')]);

test('it throw an exception when sort by the fields which is not allow..', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.users.fetch', [
        'sort' => 'first_name,-last_name,-email',
    ]));

    $response->assertStatus(Response::HTTP_BAD_REQUEST);

    expect($response->exception::class)->toBe(InvalidSortQuery::class);
});

test('it can create a user', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.users.create'), [
        'first_name' => $firstName = fake()->firstName(),
        'username' => fake()->unique()->userName(),
        'email' => $email = fake()->unique()->safeEmail(),
        'password' => 'test@452',
        'password_confirmation' => 'test@452',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(User::class, [
        'first_name' => $firstName,
        'email' => $email,
    ]);
});

test('it can delete a user', function (): void {
    $user = User::factory()->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.users.delete', [
        'id' => $user->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertSoftDeleted($user);
});

test('it can restore a user', function (): void {
    $user = User::factory()->create([
        'deleted_at' => now(),
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.users.restore', [
        'id' => $user->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    expect($user->refresh()->deleted_at)->toBeNull();

    $this->assertNotSoftDeleted($user);
});

test('it can update a user', function (): void {
    $user = User::factory()->create();

    $response = $this->withToken($this->token)->postJson(route('api.users.create', [
        'id' => $user->id,
    ]), [
        'first_name' => $firstName = fake()->firstName(),
        'username' => fake()->unique()->userName(),
        'email' => $email = fake()->unique()->safeEmail(),
        'password' => $password = 'test@452',
        'password_confirmation' => 'test@452',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(User::class, [
        'first_name' => $firstName,
        'email' => $email,
    ]);
});