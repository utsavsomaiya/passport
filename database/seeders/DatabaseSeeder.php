<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
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
            'first_name' => 'Utsav',
            'last_name' => 'Somaiya',
            'email' => 'utsav@freshbits.in',
            'username' => 'utsav',
            'password' => bcrypt('123456'),
        ]);

        User::factory()->create([
            'first_name' => 'Gaurav',
            'last_name' => 'Mackhecha',
            'email' => 'gaurav@freshbits.in',
            'username' => 'gaurav',
            'password' => bcrypt('123456'),
        ]);

        User::factory()->create([
            'first_name' => 'Dev',
            'last_name' => 'Nair',
            'email' => 'dev@artisans.com',
            'username' => 'dev',
            'password' => bcrypt('123456'),
        ]);

        Role::factory()->named('Developer')->create();
        Role::factory()->named('Super Admin')->create();
        Role::factory()->named('Access Manager')->create();

        Permission::factory()->create();
    }
}
