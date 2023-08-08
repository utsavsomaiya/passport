<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Locale;
use App\Models\LocaleProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<LocaleProduct>
 */
class LocaleProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale_id' => fn (): Collection|Model => Locale::factory()->create(),
            'product_id' => fn (): Collection|Model => Product::factory()->create(),
            'name' => fake()->name(),
            'description' => fake()->sentence(),
        ];
    }
}
