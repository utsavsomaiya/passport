<?php

declare(strict_types=1);

use App\Models\Currency;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

test('it can fetch the currencies', function (): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Super Admin');

    $currencies = Currency::factory(2)->create([
        'company_id' => $company->id,
    ]);

    $response = $this->withToken($token)->getJson(route('api.currencies.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($currency = $currencies->sortByDesc('id')->first())->id)
                                ->where('code', $currency->code)
                                ->etc()
                        )
                        ->etc()
                )
        );
});

test('it cannot fetch the currencies if they have not a proper permission', function (): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Access Manager');

    $response = $this->withToken($token)->getJson(route('api.currencies.fetch'));

    $response->assertStatus(Response::HTTP_FORBIDDEN);
});

test('it can create currency', function (): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Super Admin');

    $response = $this->withToken($token)->postJson(route('api.currencies.create'), [
        'code' => 'USD',
        'format' => '${price}',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Currency created successfully.'))
        );

    $this->assertDatabaseCount(Currency::class, 1);
});

test('it can delete currency', function (): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Super Admin');

    $currency = Currency::factory()->for($company)->create();

    $response = $this->withToken($token)->deleteJson(route('api.currencies.delete', [
        'id' => $currency->id,
    ]));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Currency deleted successfully.'))
        );

    $this->assertModelMissing($currency);
});

test('it can update currency', function (): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Super Admin');

    $currency = Currency::factory()->for($company)->create();

    $response = $this->withToken($token)->postJson(route('api.currencies.update', [
        'id' => $currency->id,
    ]), ['code' => $code = fake()->currencyCode(), 'format' => 'RM{price}']);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Currency updated successfully.'))
        );

    $this->assertDatabaseHas(Currency::class, [
        'id' => $currency->id,
        'code' => $code,
    ]);
});

test('it cannot perform any action without any proper permission', function ($data): void {
    [$user, $company, $token] = frontendApiLoginWithUser('Access Manager');

    $response = $this->withToken($token)->{$data['method'] . 'Json'}($data['route']);

    $response->assertStatus(Response::HTTP_FORBIDDEN);
})->with([
    fn (): array => ['method' => 'post', 'route' => route('api.currencies.create')],
    fn (): array => ['method' => 'delete', 'route' => route('api.currencies.delete', ['id' => fake()->uuid()])],
    fn (): array => ['method' => 'get', 'route' => route('api.currencies.fetch')],
    fn (): array => ['method' => 'post', 'route' => route('api.currencies.update', ['id' => fake()->uuid()])],
]);
