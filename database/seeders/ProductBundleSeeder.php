<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Database\Seeder;

class ProductBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bundles - https://www.notion.so/Bundles-a2923eea23954737a59d2f5f1221a5d2?pvs=4
        $bundleProduct = Product::factory()->create([
            'name' => 'Nike Weekend Set',
            'sku' => '01-14-00444-0BR04',
            'is_bundle' => true,
            'status' => true,
        ]);

        $bundleProduct->addMediaFromUrl('https://tinyurl.com/nike-weekend-set')
            ->toMediaCollection('product_images');

        $productIds = Product::whereIn('sku', ['201501004720', '201501004011', '201501000970', '201501002390'])
            ->where('is_bundle', false)
            ->pluck('id')
            ->toArray();

        $bundledProducts = [];

        foreach ($productIds as $productId) {
            $bundledProducts[] = [
                'parent_product_id' => $bundleProduct->id,
                'child_product_id' => $productId,
            ];
        }

        ProductBundle::factory()->createMany($bundledProducts);

        $bundleProduct = Product::factory()->create([
            'name' => 'Budget Attire',
            'is_bundle' => true,
            'status' => true,
        ]);

        [$shirtId, $jeansId] = $productIds;

        $bundledProducts = [
            [
                'parent_product_id' => $bundleProduct->id,
                'child_product_id' => $shirtId,
            ],
            [
                'parent_product_id' => $bundleProduct->id,
                'child_product_id' => $jeansId,
            ],
        ];

        ProductBundle::factory()->createMany($bundledProducts);
    }
}
