<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

test('it can generate the token', function (): void {
    User::factory()->create();

    $client = Client::factory()->state(fn (): array => ['personal_access_client' => true])->create();

    Passport::$personalAccessClientModel::create(['client_id' => $client->id]);

    $response = $this->postJson(route('passport.token'), [
        'grant_type' => 'client_credentials',
        'client_id' => $client->id,
        'client_secret' => $client->secret,
    ]);

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('access_token')->etc());
});

test('it can generate the personal access token', function (): void {
    [$token, $user] = passportLogin();

    $response = $this->withToken($token->accessToken)->postJson(route('api.generate_token'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Frontend',
    ]);

    $response->assertOk()->assertJsonStructure(['token']);
});
