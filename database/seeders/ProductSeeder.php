<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($companyId): void
    {
        // Shirts - https://www.notion.so/Products-2765737e7e9b4246a5541a2091eaa299?pvs=4#2e2955f4c4a74bc495d3a430c8a08dd3
        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Shirts'),
                Cache::get('Men'),
                Cache::get('Winter'),
                Cache::get('Formal'),
                Cache::pull('Formal-Slim-Fit'),
                Cache::pull('Striped'),
                Cache::get('Budget'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Men Slim Fit Striped Formal Shirt',
                'sku' => '201501004720',
                'is_bundle' => false,
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/men-striped-formal-shirt')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Shirts'),
                Cache::get('Men'),
                Cache::get('Summer'),
                Cache::get('Casual'),
                Cache::pull('Casual-Slim-Fit'),
                Cache::pull('Printed'),
                Cache::get('Mid-Range'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Men Slim Fit Printed Casual Shirt',
                'sku' => '201501004721',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/men-printed-casual-shirt')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Shirts'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::get('Casual'),
                Cache::get('High-end'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women Casual Shirt',
                'sku' => '201501004722',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/women-casual-shirt')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Shirts'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::pull('Retro'),
                Cache::pull('Mid-Range'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women Classic Retro Shirt',
                'sku' => '201501004723',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/women-classic-retro-shirts')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::pull('Shirts'),
                Cache::get('Women'),
                Cache::get('Summer'),
                Cache::get('Casual'),
                Cache::pull('Solid'),
                Cache::get('High-end'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women Solid Casual Shirt',
                'sku' => '201501004724',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/women-solid-casual-shirt')
            ->toMediaCollection('product_images');

        // Jeans
        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Jeans'),
                Cache::get('Men'),
                Cache::get('Spring'),
                Cache::get('Formal'),
                Cache::get('Skinny Fit'),
                Cache::pull('Cotton'),
                Cache::pull('Machine-wash'),
                Cache::get('Budget'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Men Skinny Fit Stretchable Jeans',
                'sku' => '201501004011',
                'is_bundle' => false,
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/men-skinny-fit-stretchable')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Jeans'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::get('Formal'),
                Cache::get('Skinny Fit'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => "Essentials Women's Skinny Jeans",
                'sku' => '201501004012',
            ]);

        $product->addMediaFromUrl('https://m.media-amazon.com/images/I/61fYUUJNpVL._AC_UY550_.jpg')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Jeans'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::get('Summer'),
                Cache::get('Formal'),
                Cache::get('Skinny Fit'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'High Waisted-Rise Colored Stretch Skinny Destroyed Ripped Distressed Jeans for Women',
                'sku' => '201501004013',
            ]);

        $product->addMediaFromUrl('https://m.media-amazon.com/images/I/81uWgpVLDzL._AC_UY550_.jpg')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Jeans'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::get('Formal'),
                Cache::get('Skinny Fit'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'luvamia Wide Leg Jeans for Women High Waisted Baggy 90S',
                'sku' => '201501004014',
            ]);

        $product->addMediaFromUrl('https://m.media-amazon.com/images/I/81uWgpVLDzL._AC_UY550_.jpg')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::pull('Jeans'),
                Cache::get('Women'),
                Cache::get('Winter'),
                Cache::get('Summer'),
                Cache::pull('Spring'),
                Cache::pull('Fall'),
                Cache::pull('Formal'),
                Cache::pull('Skinny Fit'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => "Cali1850 Women's The Everyday Garment-Dyed Skinny Jeans",
                'sku' => '201501004015',
            ]);

        $product->addMediaFromUrl('https://m.media-amazon.com/images/I/81uWgpVLDzL._AC_UY550_.jpg')
            ->toMediaCollection('product_images');

        // Sneakers
        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Sneakers'),
                Cache::get('Men'),
                Cache::get('Winter'),
                Cache::get('Summer'),
                Cache::get('Sports'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => "Rospick Slip On Men's Sneakers Walking Shoes for Men",
                'sku' => '201501000970',
                'is_bundle' => false,
            ]);

        $product->addMediaFromUrl('https://m.media-amazon.com/images/I/71PRRNkJs0L._AC_UY575_.jpg')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Sneakers'),
                Cache::get('Men'),
                Cache::pull('Winter'),
                Cache::get('Summer'),
                Cache::get('Sports'),
                Cache::get('High-end'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Men Perforations PU Mid-Top Sneakers',
                'sku' => '201501000971',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/pu-mid-top-sneakers')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Sneakers'),
                Cache::get('Women'),
                Cache::get('Summer'),
                Cache::pull('Sports'),
                Cache::pull('Mid-end'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women Perforated Club C Clean Sneakers',
                'sku' => '201501000972',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/clean-sneakers')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Sneakers'),
                Cache::get('Men'),
                Cache::get('Summer'),
                Cache::get('Casual'),
                Cache::pull('Budget'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Men Textured Sneakers',
                'sku' => '201501000973',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/men-textured-sneakers')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::pull('Sneakers'),
                Cache::get('Men'),
                Cache::pull('Summer'),
                Cache::pull('Casual'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Kids Comfort Insole Basics Sneakers With LED',
                'sku' => '201501000974',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/kids-snakers')
            ->toMediaCollection('product_images');

        // Wallets
        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Wallets'),
                Cache::get('Unisex'),
                Cache::get('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Unisex Wallets',
                'sku' => '201501002390',
                'is_bundle' => false,
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/unisex-wallets')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Wallets'),
                Cache::pull('Men'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Water Resistance Men Wallets',
                'sku' => '201501002391',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/water-resistance-wallets')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Wallets'),
                Cache::pull('Unisex'),
                Cache::pull('High-end'),
                Cache::pull('B2B'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Unisex Solid BMW M Two Fold Wallet With Brand Logo Applique Detail',
                'sku' => '201501002392',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/unisex-wallets-solid')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::get('Wallets'),
                Cache::get('Women'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women Printed PU Zip Around Wallet',
                'sku' => '201501002393',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/women-printed-wallets')
            ->toMediaCollection('product_images');

        $product = Product::factory()
            ->hasAttached(collect([
                Cache::pull('Wallets'),
                Cache::pull('Women'),
            ]), ['created_at' => now(), 'updated_at' => now()])
            ->create([
                'company_id' => $companyId,
                'name' => 'Women White Printed PU Zip Around Wallet',
                'sku' => '201501002394',
            ]);

        $product->addMediaFromUrl('https://tinyurl.com/women-zip-around-wallets')
            ->toMediaCollection('product_images');
    }
}
