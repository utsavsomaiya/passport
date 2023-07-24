<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;

beforeEach(function (): void {
    $this->token = frontendApiLoginWithUser('Super Admin')[2];
});

test('it can throw an exception when `sort` not in the pre-defined lists', function ($data): void {
    $response = $this->withToken($this->token)->getJson(route(sprintf('api.%s.fetch', $data), ['sort' => fake()->word()]));

    $response->assertStatus(Response::HTTP_BAD_REQUEST);

    expect($response->exception::class)->toBe(InvalidSortQuery::class);
})->with('modules');

test('it give an error when the wrong filter is provided.', function ($data): void {
    $response = $this->withToken($this->token)->getJson(route(sprintf('api.%s.fetch', $data), [
        'filter' => fake()->word(),
    ]));

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['filter']]);
})->with('modules');

test('it give an error when the filter is provided more than.', function ($data): void {
    $response = $this->withToken($this->token)->getJson(route(sprintf('api.%s.fetch', $data), [
        'filter' => fake()->words(10),
    ]));

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['filter']]);
})->with('modules');
