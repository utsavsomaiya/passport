<?php

declare(strict_types=1);

use App\Models\BundleProductComponent;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch the products', function (): void {
    $products = Product::factory(2)->for($this->company)->create([
        'is_bundle' => false,
    ]);

    $product = $products->sortByDesc('created_at')->first();

    $product->addMedia(UploadedFile::fake()->image('test.png'))->toMediaCollection('product_images');

    $media = $product->getFirstMedia('product_images');

    $response = $this->withToken($this->token)->getJson(route('api.products.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has('data', fn (AssertableJson $json): AssertableJson => $json
                    ->has('0', fn (AssertableJson $json): AssertableJson => $json
                        ->where('id', $product->id)
                        ->where('name', $product->name)
                        ->has('media', fn (AssertableJson $json): AssertableJson => $json
                            ->has('0', fn (AssertableJson $json): AssertableJson => $json
                                ->where('uploaded_at', $media->created_at->displayFormat())
                                ->where('url', $media->getUrl())
                            )
                            ->etc()
                        )
                        ->etc()
                    )
                    ->etc()
                )
                ->etc()
        );
});

test('if `product_status` is active or true then user needs to upload one image required validation.', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.products.create'), [
        'name' => fake()->name(),
        'sku' => fake()->uuid(),
        'upc_ean' => fake()->ean13(),
        'status' => true,
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['image']]);
});

test('it can create a product', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.products.create'), [
        'name' => $name = fake()->name(),
        'sku' => $sku = fake()->uuid(),
        'upc_ean' => fake()->ean13(),
        'status' => false,
        'image' => UploadedFile::fake()->image($image = 'test.png'),
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Product::class, [
        'name' => $name,
        'sku' => $sku,
        'status' => false,
    ]);

    $this->assertDatabaseHas(Media::class, [
        'file_name' => $image,
    ]);
});

test('it can archive(`soft delete`) the product', function (): void {
    $product = Product::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.products.delete', [
        'id' => $product->id,
    ]));

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertSoftDeleted($product);
});

test('it can update the product with its media', function (): void {
    $product = Product::factory()->for($this->company)->create();

    $product->addMedia(UploadedFile::fake()->image('test.png'))->toMediaCollection('product_images');

    $response = $this->withToken($this->token)->postJson(route('api.products.update', [
        'id' => $product->id,
    ]), [
        'name' => $name = fake()->name(),
        'sku' => fake()->uuid(),
        'status' => fake()->boolean(),
        'image' => UploadedFile::fake()->image($image = 'update.png'),
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Product::class, ['name' => $name]);

    $this->assertDatabaseHas(Media::class, ['file_name' => $image]);
});

test('it can fetch the products with its bundle components.', function (): void {
    [$parentProduct, $childProduct] = Product::factory(2)->for($this->company)
        ->sequence(['is_bundle' => true], ['is_bundle' => false])
        ->create();
    $products = [$parentProduct, $childProduct];
    $component = BundleProductComponent::factory()->create([
        'parent_product_id' => $parentProduct->id,
        'child_product_id' => $childProduct->id,
    ]);

    $parentProduct->addMedia(UploadedFile::fake()->image('test.png'))->toMediaCollection('product_images');

    $media = $parentProduct->getFirstMedia('product_images');

    $response = $this->withToken($this->token)->getJson(route('api.products.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has('data', fn (AssertableJson $json): AssertableJson => $json
                    ->has('0', fn (AssertableJson $json): AssertableJson => $json
                        ->where('id', $parentProduct->id)
                        ->where('name', $parentProduct->name)
                        ->has('bundle_components', fn (AssertableJson $json): AssertableJson => $json
                            ->has('0', fn (AssertableJson $json): AssertableJson => $json
                                ->has('component', fn (AssertableJson $json): AssertableJson => $json
                                    ->where('id', $childProduct->id)
                                    ->where('name', $childProduct->name)
                                    ->etc()
                                )
                                ->where('component_id', $component->id)
                                ->where('sort_order', $component->sort_order)
                                ->where('quantity', $component->quantity)
                            )
                            ->etc()
                        )
                        ->has('media', fn (AssertableJson $json): AssertableJson => $json
                            ->has('0', fn (AssertableJson $json): AssertableJson => $json
                                ->where('uploaded_at', $media->created_at->displayFormat())
                                ->where('url', $media->getUrl())
                            )
                            ->etc()
                        )
                        ->etc()
                    )
                    ->etc()
                )
                ->etc()
        );

});
