<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use App\Models\RolePermission;
use Facades\App\Enums\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<RolePermission>
 */
class PermissionFactory extends Factory
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
            'title' => fake()->randomElement(Permission::getFeatureGates()->toArray()),
        ];
    }
}
