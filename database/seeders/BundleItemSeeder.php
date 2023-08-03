<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BundleItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BundleItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bundleProduct = Product::factory()->create([
            'name' => 'Nike Weekend Set',
            'sku' => '01-14-00444-0BR04',
            'is_bundle' => true,
            'status' => true,
        ]);

        $bundleProduct->addMediaFromUrl('https://tinyurl.com/nike-weekend-set')
            ->toMediaCollection('product_images');

        $productIds = Product::whereIn('sku', ['201501004720', '201501004011', '201501000970', '201501002390'])
            ->pluck('id')
            ->toArray();

        $bundledProducts = [];

        foreach ($productIds as $productId) {
            $bundledProducts[] = [
                'bundle_product_id' => $bundleProduct->id,
                'child_product_id' => $productId,
            ];
        }

        BundleItem::factory()->createMany($bundledProducts);
    }
}
