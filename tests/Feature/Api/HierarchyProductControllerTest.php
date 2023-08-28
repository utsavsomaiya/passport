<?php

declare(strict_types=1);

use App\Models\Hierarchy;
use App\Models\HierarchyProduct;
use App\Models\Product;
use Illuminate\Http\Response;
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
        'is_curated' => true,
    ]);

    HierarchyProduct::create([
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $womenShirt->id,
        'is_curated' => false,
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
                    ->where('is_curated', true)
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
        'is_curated' => false,
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(HierarchyProduct::class, [
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated' => false,
    ]);
});

describe('create or update hierarchy product', function (): void {
    beforeEach(function (): void {
        $products = Product::factory(20)->for($this->company)->create();

        $this->menJeans = Product::factory()->for($this->company)->create(['name' => 'Men Jeans']);

        $this->hierarchy = Hierarchy::factory()->for($this->company)->create();

        $products->each(function (Product $product): void {
            HierarchyProduct::create([
                'hierarchy_id' => $this->hierarchy->id,
                'product_id' => $product->id,
                'is_curated' => true,
            ]);
        });
    });

    test('it cannot create or update hierarchy product when the curated product count is greater than 20', function (): void {
        $response = $this->withToken($this->token)->postJson(route('api.hierarchy_product.create_or_update'), [
            'hierarchy_id' => $this->hierarchy->id,
            'product_id' => $this->menJeans->id,
            'is_curated' => true,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    test('it can create or update hierarchy product when the curated product count is greater than 20', function (): void {
        $response = $this->withToken($this->token)->postJson(route('api.hierarchy_product.create_or_update'), [
            'hierarchy_id' => $this->hierarchy->id,
            'product_id' => $this->menJeans->id,
            'is_curated' => false,
        ]);

        $response->assertOk();
    });
});

test('it can create or update hierarchy product when the curated product count is greater than 20 but the product is from the another company', function (): void {
    $products = Product::factory(20)->create();

    $menJeans = Product::factory()->for($this->company)->create(['name' => 'Men Jeans']);

    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    $products->each(function (Product $product) use ($hierarchy): void {
        HierarchyProduct::create([
            'hierarchy_id' => $hierarchy->id,
            'product_id' => $product->id,
            'is_curated' => true,
        ]);
    });

    $response = $this->withToken($this->token)->postJson(route('api.hierarchy_product.create_or_update'), [
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated' => true,
    ]);

    $response->assertOk();
});

test('it can delete hierarchy product', function (): void {
    $menJeans = Product::factory()->for($this->company)->create(['name' => 'Men Jeans']);

    $hierarchy = Hierarchy::factory()->for($this->company)->create();

    HierarchyProduct::create([
        'hierarchy_id' => $hierarchy->id,
        'product_id' => $menJeans->id,
        'is_curated' => true,
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
