<?php

declare(strict_types=1);

use App\Models\BundleProductComponent;
use App\Models\Product;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

describe('fetch bundle product components', function (): void {
    beforeEach(function (): void {
        [$this->parentProduct, $this->firstChildProduct, $this->secondChildProduct] = Product::factory(3)
            ->for($this->company)
            ->sequence(
                ['name' => 'Jeans', 'is_bundle' => true],
                ['name' => 'Sneaker', 'is_bundle' => false],
                ['name' => 'Wallet', 'is_bundle' => false]
            )
            ->create();

        BundleProductComponent::factory()->create([
            'parent_product_id' => $this->parentProduct->id,
            'child_product_id' => $this->firstChildProduct->id,
            'sort_order' => 1,
        ]);

        $this->component = BundleProductComponent::factory()->create([
            'parent_product_id' => $this->parentProduct->id,
            'child_product_id' => $this->secondChildProduct->id,
            'sort_order' => 2,
        ]);
    });

    test('it can fetch bundle product components with default sorting', function (): void {
        $response = $this->withToken($this->token)->getJson(route('api.bundle_product_components.fetch', [
            'parentProductId' => $this->parentProduct->id,
        ]));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('data', fn (AssertableJson $json): AssertableJson => $json
                    ->has('0', fn (AssertableJson $json): AssertableJson => $json
                        ->where('component_id', $this->component->id)
                        ->has('component', fn (AssertableJson $json): AssertableJson => $json
                            ->where('id', $this->secondChildProduct->id)
                            ->etc()
                        )
                        ->where('sort_order', 2)
                        ->etc()
                    )
                    ->etc()
                )
                ->etc()
            );
    });

    test('it can display the record order by descending of component product name', function (): void {
        $response = $this->withToken($this->token)->getJson(route('api.bundle_product_components.fetch', [
            'parentProductId' => $this->parentProduct->id,
            'sort' => '-name',
        ]));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json): AssertableJson => $json
                ->has('data', fn (AssertableJson $json): AssertableJson => $json
                    ->has('0', fn (AssertableJson $json): AssertableJson => $json
                        ->where('component_id', $this->component->id)
                        ->has('component', fn (AssertableJson $json): AssertableJson => $json
                            ->where('name', 'Wallet')
                            ->etc()
                        )
                        ->where('quantity', $this->component->quantity)
                        ->where('sort_order', $this->component->sort_order)
                        ->where('component_id', $this->component->id)
                    )
                    ->etc()
                )
                ->etc()
            );
    });
});

test('it can create a bundle product with update parent product `is_bundle` column', function (): void {
    [$parentProduct, $firstChildProduct, $secondChildProduct] = Product::factory(3)->for($this->company)->create(['is_bundle' => false]);

    $response = $this->withToken($this->token)->postJson(route('api.bundle_product_components.create', [
        'parentProductId' => $parentProduct->id,
    ]), [
        'bundle_product_components' => [
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

    $this->assertDatabaseHas(BundleProductComponent::class, [
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $firstChildProduct->id,
        'sort_order' => 2,
    ]);

    $this->assertDatabaseHas(BundleProductComponent::class, [
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $secondChildProduct->id,
        'sort_order' => null,
    ]);

    expect($parentProduct->refresh())->is_bundle->toBeTrue();
});

test('it can delete the product bundle', function (): void {
    [$parentProduct, $childProduct] = Product::factory(2)->for($this->company)
        ->sequence(['is_bundle' => true], ['is_bundle' => false])
        ->create();

    $component = BundleProductComponent::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $childProduct->id,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.bundle_product_components.delete', [
        'id' => $component->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertModelMissing($component);

    expect($parentProduct->refresh())->is_bundle->toBeFalse();
});

test('it cannot modify the `is_bundle` column if there is already a product within the bundle when attempting to delete a component', function (): void {
    [$parentProduct, $firstChildProduct, $secondChildProduct] = Product::factory(3)->for($this->company)
        ->sequence(['is_bundle' => true], ['is_bundle' => false])
        ->create();

    $firstBundleProductComponent = BundleProductComponent::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $firstChildProduct->id,
    ]);

    BundleProductComponent::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $secondChildProduct->id,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.bundle_product_components.delete', [
        'id' => $firstBundleProductComponent->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertModelMissing($firstBundleProductComponent);

    expect($parentProduct->refresh())->is_bundle->toBeTrue();
});

test('it can update the product bundle', function (): void {
    [$parentProduct, $childProduct] = Product::factory(2)->for($this->company)
        ->sequence(['is_bundle' => true], ['is_bundle' => false])
        ->create();

    $component = BundleProductComponent::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $childProduct->id,
        'quantity' => 2,
        'sort_order' => null,
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.bundle_product_components.update', [
        'id' => $component->id,
    ]), [
        'quantity' => 5,
        'sort_order' => 1,
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(BundleProductComponent::class, [
        'id' => $component->id,
        'quantity' => 5,
        'sort_order' => 1,
    ]);
});
