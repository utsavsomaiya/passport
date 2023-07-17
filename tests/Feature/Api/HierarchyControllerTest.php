<?php

declare(strict_types=1);

use App\Models\Hierarchy;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch hierarchies', function (): void {
    $hierarchy = Hierarchy::factory()
        ->company($this->company->id)
        ->has(
            Hierarchy::factory()->company($this->company->id)->state(fn (): array => ['name' => 'B2B']),
            'children'
        )
        ->create(['name' => 'B2B']);

    $response = $this->withToken($this->token)->getJson(route('api.hierarchies.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', $hierarchy->id)
                                ->where('name', $hierarchy->name)
                                ->count('children', $hierarchy->children->count())
                                ->etc()
                        )
                        ->etc()
                )
        );
});

test('it can create a hierarchy', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.hierarchies.create'), [
        'name' => 'B2B',
        'description' => 'This is for the B2B channel',
        'slug' => $slug = 'Business to business',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Hierarchy created successfully.'))
        );

    $this->assertDatabaseHas(Hierarchy::class, [
        'name' => 'B2B',
        'slug' => $slug,
    ]);
});

test('it can create a child hierarchy', function (): void {
    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.hierarchies.create', [
        'parent' => $hierarchy->id,
    ]), [
        'name' => 'B2B',
        'description' => 'This is for the B2B channel',
        'slug' => 'Business to business',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Hierarchy created successfully.'))
        );

    $this->assertDatabaseHas(Hierarchy::class, [
        'name' => 'B2B',
        'parent_hierarchy_id' => $hierarchy->id,
    ]);
});

test('it cannot create a child hierarchy if parent does not exist', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.hierarchies.create', [
        'parent' => fake()->uuid(),
    ]), [
        'name' => 'B2B',
        'description' => 'This is for the B2B channel',
        'slug' => 'Business to business',
    ]);

    $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
});

test('it can delete a hierarchy', function (): void {
    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.hierarchies.delete', [
        'id' => $hierarchy->id,
    ]));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Hierarchy deleted successfully.'))
        );

    $this->assertModelMissing($hierarchy);
});

test('it cannot delete a hierarchy if it has children', function (): void {
    $hierarchy = Hierarchy::factory()->for($this->company)
        ->has(Hierarchy::factory()->for($this->company), 'children')
        ->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.hierarchies.delete', [
        'id' => $hierarchy->id,
    ]));

    $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
});

test('it can update a hierarchy', function (): void {
    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.hierarchies.update', [
        'id' => $hierarchy->id,
    ]), [
        'name' => $name = 'Seasonality',
        'description' => 'This is for only those seasons',
        'slug' => $slug = 'Seasonality',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Hierarchy updated successfully.'))
        );

    $this->assertDatabaseHas(Hierarchy::class, [
        'id' => $hierarchy->id,
        'name' => $name,
        'slug' => $slug,
    ]);
});
