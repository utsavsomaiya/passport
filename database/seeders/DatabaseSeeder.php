<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Console\Command;

class DatabaseSeeder extends GenerateCsvDataSeeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->warn(PHP_EOL.'Creating users...');
        $success = $this->seedDataFromCsvFile(database_path('/seeders/csv/users.csv'));
        if ($success === Command::SUCCESS) {
            $this->command->info('Users created');
        }
    }
}
