<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fn (): Collection|Model => Company::factory()->create(),
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'sku' => strtoupper(substr(md5(uniqid()), 0, 10)),
            'upc_ean' => fake()->ean13(),
            'external_reference' => fake()->url(),
            'status' => fake()->boolean(),
            'is_bundle' => false,
        ];
    }
}
