<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

test('it can generate the user token', function (): void {
    $user = User::factory()->create();

    $response = $this->postJson(route('api.generate_token'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'test'
    ]);

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('token'));
});
