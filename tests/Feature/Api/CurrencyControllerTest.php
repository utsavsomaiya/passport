<?php

declare(strict_types=1);

use App\Models\Currency;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch the currencies', function (): void {
    $currencies = Currency::factory(2)->create([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->getJson(route('api.currencies.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($currency = $currencies->sortByDesc('created_at')->first())->id)
                                ->where('code', $currency->code)
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});

test('it can create currency', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.currencies.create'), [
        'code' => 'USD',
        'format' => '${price}',
        'is_default' => '1',
        'status' => '1',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Currency created successfully.'))
        );

    $this->assertDatabaseCount(Currency::class, 1);
});

test('it can delete currency', function (): void {
    $currency = Currency::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.currencies.delete', [
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
    $currency = Currency::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.currencies.update', [
        'id' => $currency->id,
    ]), ['code' => $code = fake()->currencyCode(), 'format' => 'RM{price}', 'status' => '1', 'is_default' => '1']);

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
