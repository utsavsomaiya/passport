<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RolePermission>
 */
class RolePermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => fn (): Collection|Model => Role::factory()->create(),
            'permission' => fake()->numberBetween(1, 255), // This will change after we make an enum
        ];
    }
}
