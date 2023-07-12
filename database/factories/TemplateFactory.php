<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
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
            'name' => fake()->word(),
            'description' => fake()->sentence(),
        ];
    }
}
