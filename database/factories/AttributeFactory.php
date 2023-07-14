<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FieldType;
use App\Models\Attribute;
use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'template_id' => fn (): Collection|Model => Template::factory()->create(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'slug' => fake()->slug(),
            'field_type' => $value = fake()->randomElement(FieldType::values()),
            'validation' => FieldType::tryFrom($value)->validation(),
            'is_required' => fake()->boolean(),
            'status' => fake()->boolean(),
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
