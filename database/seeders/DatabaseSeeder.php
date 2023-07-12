<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Database\Factories\CompanyFactory;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Console\View\Components\TwoColumnDetail;

class DatabaseSeeder extends GenerateCsvDataSeeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $time = microtime(true);

        $this->fakerCompanySeeding();

        $companyMinimumId = Company::min('id');

        $this->seedDataFromCsvFile(database_path('/seeders/csv/users.csv'));

        $this->seedDataFromCsvFile(database_path('/seeders/csv/roles.csv'));

        User::cursor()->each(function (User $user): void {
            $user->assignRole(['Super Admin']);
            $user->companies()->attach(Company::min('id'), ['created_at' => now(), 'updated_at' => now()]);
        });

        $this->seedDataFromCsvFile(database_path('/seeders/csv/locales.csv'), companyId: $companyMinimumId);

        $this->seedDataFromCsvFile(database_path('/seeders/csv/currencies.csv'), companyId: $companyMinimumId);

        $this->seedDataFromCsvFile(database_path('/seeders/csv/price_books.csv'), companyId: $companyMinimumId);

        $this->call(HierarchySeeder::class, parameters: ['companyId' => $companyMinimumId]);

        $this->seedDataFromCsvFile(database_path('/seeders/csv/templates.csv'), companyId: $companyMinimumId);

        app()->make(Factory::class, ['output' => $this->command->getOutput()])
            ->info('All this took '.number_format((microtime(true) - $time) * 1000, 2).' ms');
    }

    private function fakerCompanySeeding(): void
    {
        with(new TwoColumnDetail($this->command->getOutput()))->render(CompanyFactory::class, '<fg=yellow;options=bold>RUNNING</>');

        $startTime = microtime(true);

        Company::factory(2)->create();

        $runTime = number_format((microtime(true) - $startTime) * 1000, 2);

        with(new TwoColumnDetail($this->command->getOutput()))->render(CompanyFactory::class, sprintf('<fg=gray>%s ms</> <fg=green;options=bold>DONE</>', $runTime));
        $this->command->getOutput()->writeln('');
    }
}
