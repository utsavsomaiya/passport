<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hierarchy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($companyId): void
    {
        // B2B - https://www.notion.so/Hierarchies-9fe7d1d34c7a43d5b3e3ac551d451409?pvs=4#2ba3c7445d8349d6839630cbf1b54479
        $hierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory()->company($companyId)->state(fn (): array => ['name' => 'B2B']),
                'children'
            )
            ->create(['name' => 'B2B']);

        Cache::put('B2B', $hierarchy->children->first());

        // Type
        $typeHierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(4)->company($companyId)->sequence(
                    ['name' => 'Shirts'],
                    ['name' => 'Jeans'],
                    ['name' => 'Sneakers'],
                    ['name' => 'Wallets']
                ),
                'children'
            )
            ->create(['name' => 'Type']);

        Cache::put('Shirts', $typeHierarchy->children->firstWhere('name', 'Shirts'));
        Cache::put('Jeans', $typeHierarchy->children->firstWhere('name', 'Jeans'));
        Cache::put('Sneakers', $typeHierarchy->children->firstWhere('name', 'Sneakers'));
        Cache::put('Wallets', $typeHierarchy->children->firstWhere('name', 'Wallets'));

        // Gender
        $genderHierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(3)->company($companyId)->sequence(
                    ['name' => "Men's"],
                    ['name' => "Women's"],
                    ['name' => 'Unisex'],
                ),
                'children'
            )
            ->create(['name' => 'Gender']);

        Cache::put('Men', $genderHierarchy->children->firstWhere('name', "Men's"));
        Cache::put('Women', $genderHierarchy->children->firstWhere('name', "Women's"));
        Cache::put('Unisex', $genderHierarchy->children->firstWhere('name', 'Unisex'));

        // Price Range
        $priceRangeHierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(3)->company($companyId)->sequence(
                    ['name' => 'Budget'],
                    ['name' => 'Mid-Range'],
                    ['name' => 'High-End'],
                ),
                'children'
            )
            ->create(['name' => 'Price Range']);

        Cache::put('Budget', $priceRangeHierarchy->children->firstWhere('name', 'Budget'));
        Cache::put('Mid-Range', $priceRangeHierarchy->children->firstWhere('name', 'Mid-Range'));
        Cache::put('High-End', $priceRangeHierarchy->children->firstWhere('name', 'High-End'));

        // Seasonality
        $seasonalityHierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(4)->company($companyId)->sequence(
                    ['name' => 'Winter'],
                    ['name' => 'Summer'],
                    ['name' => 'Spring'],
                    ['name' => 'Fall']
                ),
                'children'
            )
            ->create(['name' => 'Seasonality']);

        Cache::put('Winter', $seasonalityHierarchy->children->firstWhere('name', 'Winter'));
        Cache::put('Summer', $seasonalityHierarchy->children->firstWhere('name', 'Summer'));
        Cache::put('Spring', $seasonalityHierarchy->children->firstWhere('name', 'Spring'));
        Cache::put('Fall', $seasonalityHierarchy->children->firstWhere('name', 'Fall'));

        // Style
        $styleHierarChy = Hierarchy::factory()
            ->has(
                Hierarchy::factory(4)
                    ->company($companyId)
                    ->sequence(
                        ['name' => 'Casual'],
                        ['name' => 'Formal'],
                        ['name' => 'Retro'],
                        ['name' => 'Sports'],
                    ),
                'children'
            )
            ->company($companyId)
            ->create(['name' => 'Style']);

        Cache::put('Casual', $styleHierarChy->children->firstWhere('name', 'Casual'));
        Cache::put('Formal', $styleHierarChy->children->firstWhere('name', 'Formal'));
        Cache::put('Retro', $styleHierarChy->children->firstWhere('name', 'Retro'));
        Cache::put('Sports', $styleHierarChy->children->firstWhere('name', 'Sports'));

        $casualHierarchy = Hierarchy::factory(3)
            ->company($companyId)
            ->sequence(
                ['name' => 'Slim fit'],
                ['name' => 'Printed'],
                ['name' => 'Solid'],
            )
            ->create(['parent_hierarchy_id' => cache('Casual')->id]);

        Cache::put('Casual-Slim-Fit', $casualHierarchy->firstWhere('name', 'Slim fit'));
        Cache::put('Printed', $casualHierarchy->firstWhere('name', 'Printed'));
        Cache::put('Solid', $casualHierarchy->firstWhere('name', 'Solid'));

        $formalHierarchy = Hierarchy::factory(3)
            ->company($companyId)
            ->sequence(
                ['name' => 'Slim fit'],
                ['name' => 'Skinny Fit'],
                ['name' => 'Striped'],
            )
            ->create(['parent_hierarchy_id' => cache('Formal')->id]);

        Cache::put('Formal-Slim-Fit', $formalHierarchy->firstWhere('name', 'Slim fit'));
        Cache::put('Skinny Fit', $formalHierarchy->firstWhere('name', 'Skinny Fit'));
        Cache::put('Striped', $formalHierarchy->firstWhere('name', 'Striped'));

        // Material
        $materialHierarchy = Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(2)->company($companyId)->sequence(
                    ['name' => 'Cotton'],
                    ['name' => 'Machine-wash'],
                ),
                'children'
            )
            ->create(['name' => 'Material']);

        Cache::put('Cotton', $materialHierarchy->children->firstWhere('name', 'Cotton'));
        Cache::put('Machine-wash', $materialHierarchy->children->firstWhere('name', 'Machine-wash'));
    }
}
