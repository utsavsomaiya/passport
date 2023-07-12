<?php

declare(strict_types=1);

use App\Models\PriceBook;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch price books', function (): void {
    $priceBook = PriceBook::factory()->for($this->company)->create(['name' => 'B2B']);

    $response = $this->withToken($this->token)->getJson(route('api.price_books.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', $priceBook->id)
                                ->where('name', $priceBook->name)
                                ->etc()
                        )
                        ->etc()
                )
        );
});

test('it can create a price book', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.price_books.create'), [
        'name' => 'B2B',
        'description' => 'This price book for the B2B channel',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Price book created successfully.'))
        );

    $this->assertDatabaseHas(PriceBook::class, [
        'name' => 'B2B',
    ]);
});

test('it can delete a price book', function (): void {
    $priceBook = PriceBook::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.price_books.delete', [
        'id' => $priceBook->id,
    ]));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Price book deleted successfully.'))
        );

    $this->assertModelMissing($priceBook);
});

test('it can update a price book', function (): void {
    $priceBook = PriceBook::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.price_books.update', [
        'id' => $priceBook->id,
    ]), [
        'name' => $name = 'Online',
        'description' => 'This is for only online channel',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Price book updated successfully.'))
        );

    $this->assertDatabaseHas(PriceBook::class, [
        'id' => $priceBook->id,
        'name' => $name,
    ]);
});
