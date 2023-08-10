<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Product',
            'last_name' => 'Xperience Manager',
            'username' => 'product_xp_manager',
            'email' => 'contact@artisanssolutions.com',
            'password' => bcrypt('PxM#2023$Secure'),
        ]);

        Role::factory()->named('Super Admin')->create([
            'description' => 'This role provides full access to the panel.',
        ]);

        $user->assignRoles(['Super Admin']);

        $this->command->info('A user has been successfully created with the <options=bold>Super Admin</> role.');
    }
}
