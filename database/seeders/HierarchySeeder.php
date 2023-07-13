<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hierarchy;
use Illuminate\Database\Seeder;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($companyId): void
    {
        // B2B - https://www.notion.so/Hierarchies-9fe7d1d34c7a43d5b3e3ac551d451409?pvs=4#2ba3c7445d8349d6839630cbf1b54479
        Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory()->company($companyId)->state(fn (): array => ['name' => 'B2B']),
                'children'
            )
            ->create(['name' => 'B2B']);

        // Type
        Hierarchy::factory()
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

        // Gender
        Hierarchy::factory()
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

        // Seasonality
        Hierarchy::factory()
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

        Hierarchy::factory(3)
            ->company($companyId)
            ->sequence(
                ['name' => 'Slim fit'],
                ['name' => 'Printed'],
                ['name' => 'Solid'],
            )
            ->create(['parent_hierarchy_id' => $styleHierarChy->children->first()->id]);

        Hierarchy::factory(3)
            ->company($companyId)
            ->sequence(
                ['name' => 'Slim fit'],
                ['name' => 'Printed'],
                ['name' => 'Solid'],
            )
            ->create(['parent_hierarchy_id' => $styleHierarChy->children->firstWhere('name', 'Formal')->id]);

        // Material
        Hierarchy::factory()
            ->company($companyId)
            ->has(
                Hierarchy::factory(2)->company($companyId)->sequence(
                    ['name' => 'Cotton'],
                    ['name' => 'Machine-wash'],
                ),
                'children'
            )
            ->create(['name' => 'Material']);
    }
}
