<?php

declare(strict_types=1);

use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('it can send an email of the forgot password', function (): void {
    [$token] = passportLogin();

    $user = UserFactory::new(['email' => $email = 'test@gmail.com'])->create();

    Notification::fake();

    Notification::assertNothingSent();

    $response = $this->withToken($token->accessToken)->postJson(route('api.forgot_password'), [
        'email' => $email,
        'reset_password_page_url' => fake()->url(),
    ]);

    Notification::assertSentTo([$user], ResetPassword::class);

    $response->assertOk()->assertJsonStructure(['success']);
});
