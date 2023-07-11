<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;

class DatabaseSeeder extends GenerateCsvDataSeeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $time = microtime(true);

        $this->command->warn('Creating companies...');
        Company::factory(2)->create();
        $this->command->info('Companies created.');

        $companyMinimumId = Company::min('id');

        $this->command->warn(PHP_EOL.'Creating users...');

        $success = $this->seedDataFromCsvFile(database_path('/seeders/csv/users.csv'));
        if ($success === Command::SUCCESS) {
            $this->command->info('Users created.');
        }

        $this->command->warn(PHP_EOL.'Creating roles');
        $success = $this->seedDataFromCsvFile(database_path('/seeders/csv/roles.csv'));
        if ($success === Command::SUCCESS) {
            $this->command->info('Roles created.');
        }

        User::cursor()->each(function (User $user): void {
            $user->assignRole(['Super Admin']);
            $user->companies()->attach(Company::min('id'), ['created_at' => now(), 'updated_at' => now()]);
        });

        $this->command->warn(PHP_EOL.'Creating Locales...');
        $success = $this->seedDataFromCsvFile(database_path('/seeders/csv/locales.csv'), companyId: $companyMinimumId);
        if ($success === Command::SUCCESS) {
            $this->command->info('Locales created.');
        }

        $this->command->warn(PHP_EOL.'Creating Currencies...');
        $success = $this->seedDataFromCsvFile(database_path('/seeders/csv/currencies.csv'), companyId: $companyMinimumId);
        if ($success === Command::SUCCESS) {
            $this->command->info('Currencies created.');
        }

        $this->command->newLine();
        $this->call(HierarchySeeder::class, parameters: ['companyId' => $companyMinimumId]);

        $secs = microtime(true) - $time;
        app()->make(Factory::class, ['output' => $this->command->getOutput()])
            ->info('All this took '.round($secs * 1000).'ms');
    }
}
