<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Hierarchy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Hierarchy>
 */
class HierarchyFactory extends Factory
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
            'parent_hierarchy_id' => null,
            'name' => fake()->word(),
            'description' => fake()->sentence(),
        ];
    }

    public function company(string $id): static
    {
        return $this->state(fn (): array => ['company_id' => $id]);
    }
}
