<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<ProductBundle>
 */
class ProductBundleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_product_id' => fn (): Collection|Model => Product::factory()->create(),
            'child_product_id' => fn (): Collection|Model => Product::factory()->create(),
            'quantity' => fake()->numberBetween(5, 10),
            'sort_order' => fake()->numberBetween(5, 10),
        ];
    }
}
