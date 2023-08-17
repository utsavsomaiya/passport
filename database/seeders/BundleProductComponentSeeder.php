<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BundleProductComponent;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BundleProductComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($companyId): void
    {
        // Bundles - https://www.notion.so/Bundles-a2923eea23954737a59d2f5f1221a5d2?pvs=4
        $parentProduct = Product::factory()->create([
            'company_id' => $companyId,
            'name' => 'Nike Weekend Set',
            'sku' => '01-14-00444-0BR04',
            'is_bundle' => true,
            'status' => true,
        ]);

        $parentProduct->addMediaFromUrl('https://tinyurl.com/nike-weekend-set')
            ->toMediaCollection('product_images');

        $bundledProductComponents = [];

        $productIds = Product::whereIn('sku', ['201501004720', '201501004011', '201501000970', '201501002390'])
            ->where('is_bundle', false)
            ->pluck('id')
            ->each(function ($childProductId) use (&$bundledProductComponents, $parentProduct): void {
                $bundledProductComponents[] = [
                    'parent_product_id' => $parentProduct->id,
                    'child_product_id' => $childProductId,
                ];
            })
            ->toArray();

        BundleProductComponent::factory()->createMany($bundledProductComponents);

        $parentProduct = Product::factory()->create([
            'company_id' => $companyId,
            'name' => 'Budget Attire',
            'is_bundle' => true,
            'status' => true,
        ]);

        [$shirtId, $jeansId] = $productIds;

        $bundledProductComponents = [
            [
                'parent_product_id' => $parentProduct->id,
                'child_product_id' => $shirtId,
            ],
            [
                'parent_product_id' => $parentProduct->id,
                'child_product_id' => $jeansId,
            ],
        ];

        BundleProductComponent::factory()->createMany($bundledProductComponents);
    }
}
