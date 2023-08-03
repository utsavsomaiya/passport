<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BundleItem>
 */
class BundleItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bundle_product_id' => fn () => Product::factory()->create()->parent_product_id,
            'child_product_id' => fn () => Product::factory()->create(),
            'quantity' => fake()->numberBetween(5, 10),
            'order' => fake()->numberBetween(5, 10),
        ];
    }
}
