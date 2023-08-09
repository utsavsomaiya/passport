<?php

declare(strict_types=1);

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');

    $this->product = Product::factory()->for($this->company)->create();
});

test('it can fetch product images', function (): void {
    $this->product->media()->create([
        'name' => 'abc',
        'file_name' => 'abc.png',
        'collection_name' => 'product_images',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 286177,
        'manipulations' => [],
        'custom_properties' => [],
        'responsive_images' => [],
        'generated_conversions' => [],
        'order_column' => 1,
    ]);

    $response = $this->withToken($this->token)->getJson(route('api.product_media.fetch', [
        'productId' => $this->product->id,
    ]));

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', fn (AssertableJson $json): AssertableJson => $json
                ->has('0', fn (AssertableJson $json): AssertableJson => $json
                    ->where('id', $this->product->getFirstMedia('product_images')->id)
                    ->etc()
                )
                ->etc()
            )
        );
});

test('it can upload product images', function (): void {
    $file = UploadedFile::fake()->image('abc.jpg');

    $response = $this->withToken($this->token)->postJson(route('api.product_media.create', [
        'productId' => $this->product->id,
    ]), [
        'images' => [$file],
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Media::class, [
        'model_id' => $this->product->id,
    ]);
});

test('it can delete the media', function (): void {
    $this->product->media()->create([
        'name' => 'abc',
        'file_name' => 'abc.png',
        'collection_name' => 'product_images',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 286177,
        'manipulations' => [],
        'custom_properties' => [],
        'responsive_images' => [],
        'generated_conversions' => [],
        'order_column' => 1,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.product_media.delete', [
        'productId' => $this->product->id,
        'id' => ($media = $this->product->getFirstMedia('product_images'))->id,
    ]));

    $response->assertOk();

    $this->assertModelMissing($media);
});
