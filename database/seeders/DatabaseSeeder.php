<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'user',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
        ]);

        Role::factory()->create([
            'name' => 'Permission Manager',
        ]);
    }
}
