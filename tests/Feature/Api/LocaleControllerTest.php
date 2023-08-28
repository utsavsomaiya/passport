<?php

declare(strict_types=1);

use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch locales', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.locales.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'locales',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has('30', fn (AssertableJson $json): AssertableJson => $json
                            ->where('id', 31)
                            ->where('name', 'Haitian Creole')
                            ->where('code', 'ht')
                        )
                        ->etc()
                )
                ->etc()
        );
});
