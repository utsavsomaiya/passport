<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $utsav = User::factory()->create([
            'first_name' => 'Utsav',
            'last_name' => 'Somaiya',
            'email' => 'utsav@freshbits.in',
            'username' => 'utsav',
            'password' => bcrypt('123456'),
        ]);

        $gaurav = User::factory()->create([
            'first_name' => 'Gaurav',
            'last_name' => 'Mackhecha',
            'email' => 'gaurav@freshbits.in',
            'username' => 'gaurav',
            'password' => bcrypt('123456'),
        ]);

        $dev = User::factory()->create([
            'first_name' => 'Dev',
            'last_name' => 'Nair',
            'email' => 'dev@artisans.com',
            'username' => 'dev',
            'password' => bcrypt('123456'),
        ]);

        $developer = Role::factory()->named('Developer')->create();
        $superAdmin = Role::factory()->named('Super Admin')->create();
        $accessManager = Role::factory()->named('Access Manager')->create();

        $utsav->assignRole($developer);
        $gaurav->assignRole($superAdmin);
        $dev->assignRole([$accessManager, $superAdmin]);

        // This data is static data because of we are playing with real world!!
        $this->displayUserData(collect([$utsav, $gaurav, $dev]), collect([$developer, $superAdmin, $accessManager]));
    }

    private function displayUserData(Collection $users, Collection $roles): void
    {
        $this->command->table(['First Name', 'Last Name', 'Email', 'Password', 'Roles'], [
            [
                'first_name' => $users->first()->first_name,
                'last_name' => $users->first()->last_name,
                'email' => $users->first()->email,
                'password' => '123456',
                'roles' => $roles->first()->name,
            ],
            [
                'first_name' => $users[1]->first_name,
                'last_name' => $users[1]->last_name,
                'email' => $users[1]->email,
                'password' => '123456',
                'roles' => $roles[1]->name,
            ],
            [
                'first_name' => $users->last()->first_name,
                'last_name' => $users->last()->last_name,
                'email' => $users->last()->email,
                'password' => '123456',
                'roles' => $roles[1]->name . ', ' . $roles->last()->name,
            ],
        ]);
    }
}
