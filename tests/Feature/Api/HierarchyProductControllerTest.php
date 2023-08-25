<?php

declare(strict_types=1);

use App\Models\Hierarchy;
use App\Models\HierarchyProduct;
use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it will fetch curated products of hierarchy', function (): void {
    [$menJeans, $womenShirt] = Product::factory(2)
        ->for($this->company)
        ->sequence(
            ['name' => 'Men Jeans'],
            ['name' => 'Women Shirt'],
        )
        ->create();

    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    HierarchyProduct::create([
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated_product' => true,
    ]);

    HierarchyProduct::create([
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $womenShirt->id,
        'is_curated_product' => false,
    ]);

    $response = $this->withToken($this->token)->getJson(route('api.hierarchy_product.fetch', [
        'hierarchyId' => $hierarchy->id,
    ]));

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('0', fn (AssertableJson $json): AssertableJson => $json
                    ->where('id', $menJeans->id)
                    ->where('name', $menJeans->name)
                    ->where('slug', $menJeans->slug)
                    ->where('is_curated_product', true)
                    ->etc()
                )
                ->etc()
            )
            ->etc()
        );
});

test('it can create or update hierarchy product', function (): void {
    $menJeans = Product::factory()->for($this->company)->create(['name' => 'Men Jeans']);

    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.hierarchy_product.create_or_update'), [
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated_product' => false,
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(HierarchyProduct::class, [
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated_product' => false,
    ]);
});

test('it can delete hierarchy product', function (): void {
    $menJeans = Product::factory()->for($this->company)->create(['name' => 'Men Jeans']);

    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    HierarchyProduct::create([
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated_product' => true,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.hierarchy_product.delete', [
        'hierarchyId' => $hierarchy->id,
        'productId' => $menJeans->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseMissing(HierarchyProduct::class, [
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
    ]);
});
