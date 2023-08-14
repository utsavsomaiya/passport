<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch bundle product items', function (): void {
    $products = Product::factory(3)
        ->for($this->company)
        ->sequence(['is_bundle' => true], ['is_bundle' => false], ['is_bundle' => false])
        ->create();

    $bundleProduct = $products->first();

    $nonBundleProducts = array_values($products->filter(fn ($product): bool => $product->is_bundle === false)->all());

    ProductBundle::factory()->create([
        'parent_product_id' => $bundleProduct->id,
        'child_product_id' => $nonBundleProducts[0]->id,
        'sort_order' => 1,
    ]);

    $productBundle = ProductBundle::factory()->create([
        'parent_product_id' => $bundleProduct->id,
        'child_product_id' => $nonBundleProducts[1]->id,
        'sort_order' => 2,
    ]);

    $response = $this->withToken($this->token)->getJson(route('api.product_bundles.fetch', [
        'productId' => $bundleProduct->id,
    ]));

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('0', fn (AssertableJson $json): AssertableJson => $json
                    ->where('bundle_id', $productBundle->id)
                    ->where('id', $nonBundleProducts[1]->id)
                    ->etc()
                )
                ->etc()
            )
            ->etc()
        );
});

test('it can create a bundle product with update parent product `is_bundle` column', function (): void {
    [$parentProduct, $firstChildProduct, $secondChildProduct] = Product::factory(3)->for($this->company)->create(['is_bundle' => false]);

    $response = $this->withToken($this->token)->postJson(route('api.product_bundles.create', [
        'productId' => $parentProduct->id,
    ]), [
        'bundle_products' => [
            [
                'id' => $firstChildProduct->id,
                'quantity' => fake()->numberBetween(1, 10),
                'sort_order' => 2,
            ],
            [
                'id' => $secondChildProduct->id,
                'quantity' => fake()->numberBetween(1, 10),
            ],
        ],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(ProductBundle::class, [
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $firstChildProduct->id,
        'sort_order' => 2,
    ]);

    $this->assertDatabaseHas(ProductBundle::class, [
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $secondChildProduct->id,
        'sort_order' => null,
    ]);

    expect($parentProduct->refresh())->is_bundle->toBeTrue();
});

test('it can delete the product bundle', function (): void {
    [$parentProduct, $childProduct] = Product::factory(2)->for($this->company)
        ->sequence(
            ['is_bundle' => true],
            ['is_bundle' => false]
        )
        ->create();

    $productBundle = ProductBundle::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $childProduct->id,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.product_bundles.delete', [
        'id' => $productBundle->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertModelMissing($productBundle);

    expect($parentProduct->refresh())->is_bundle->toBeFalse();
});

test('it cannot update the `is_bundle` column when already one product in bundle', function (): void {
    [$parentProduct, $firstChildProduct, $secondChildProduct] = Product::factory(3)->for($this->company)
        ->sequence(
            ['is_bundle' => true],
            ['is_bundle' => false]
        )
        ->create();

    $firstBundle = ProductBundle::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $firstChildProduct->id,
    ]);

    ProductBundle::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $secondChildProduct->id,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.product_bundles.delete', [
        'id' => $firstBundle->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertModelMissing($firstBundle);

    expect($parentProduct->refresh())->is_bundle->toBeTrue();
});

test('it can update the product bundle', function (): void {
    [$parentProduct, $childProduct] = Product::factory(2)->for($this->company)
        ->sequence(
            ['is_bundle' => true],
            ['is_bundle' => false]
        )
        ->create();

    $productBundle = ProductBundle::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $childProduct->id,
        'quantity' => 2,
        'sort_order' => null,
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.product_bundles.update', [
        'id' => $productBundle->id,
    ]), [
        'quantity' => 5,
        'sort_order' => 1,
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(ProductBundle::class, [
        'id' => $productBundle->id,
        'quantity' => 5,
        'sort_order' => 1,
    ]);
});
