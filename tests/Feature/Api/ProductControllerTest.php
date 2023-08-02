<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch the products', function (): void {
    $products = Product::factory(2)->for($this->company)->create();

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

});

test('it can create a product', function (): void {

});
