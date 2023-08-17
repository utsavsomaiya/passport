<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BundleProductComponent;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BundleProductComponent>
 */
class BundleProductComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_product_id' => fn () => Product::factory()->create(['is_bundle' => true]),
            'child_product_id' => fn () => Product::factory()->create(['is_bundle' => false]),
            'quantity' => fake()->numberBetween(1, 100),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
