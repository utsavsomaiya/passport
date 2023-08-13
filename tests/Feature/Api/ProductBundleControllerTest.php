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

    $nonBundleProducts = array_values($products->filter(fn ($product) => $product->is_bundle === false)->all());

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

    $response = $this->withToken($this->token)->getJson(route('api.product_bundle.fetch_items', [
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
