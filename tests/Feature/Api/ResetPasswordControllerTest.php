<?php

declare(strict_types=1);

use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

test('it can reset the password using the password reset token', function (): void {
    $user = UserFactory::new(['email' => 'test@gmail.com'])->create();

    $token = Password::createToken($user);

    $response = $this->postJson(route('api.reset_password'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'user@645',
        'password_confirmation' => 'user@645',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertTrue(Hash::check('user@645', $user->refresh()->password));
});
