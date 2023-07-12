<?php

declare(strict_types=1);

use Illuminate\Http\Response;

test('it cannot perform any action without any proper permission', function ($data): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Access Manager');

    $response = $this->withToken($token)->{$data['method'] . 'Json'}($data['route']);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
})->with(getRoutes([
    'locales',
    'currencies',
    'hierarchies',
    'price_books',
]));
