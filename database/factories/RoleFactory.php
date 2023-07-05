<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Access Manager', 'Admin', 'Developer']),
            'description' => fake()->sentence(),
            'guard_name' => config('auth.defaults.guard'),
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn (): array => [
            'name' => $name,
        ]);
    }
}
