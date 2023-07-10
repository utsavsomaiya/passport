<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateCsvDataSeeder extends Seeder
{
    protected function seedDataFromCsvFile(string $filename, string $table = null, string $companyId = null): int
    {
        $csvFile = @fopen($filename, 'r');

        if (is_bool($csvFile)) {
            $this->command->line('<options=bold;fg=red>Bro!! Are you drunk..? 🙄</>');

            return Command::FAILURE;
        }

        if (is_null($table)) {
            $table = Str::of($filename)->basename('.csv')->value();
        }

        if ([] === $columns = Schema::getColumnListing($table)) {
            $selections = $this->command->choice('Bro!! There are 2 possibilities', [
                1 => $firstPossibility = sprintf('<fg=yellow>Maybe you forgot to name the file the same as your <options=bold>%s</> table!</>', $table),
                2 => '<options=bold;fg=yellow>Or you forgot to run the migration before seeding!</>',
            ]);

            if ($selections === $firstPossibility) {
                $this->renameFile($filename);

                return Command::FAILURE;
            }

            $this->runMigrations();

            return Command::FAILURE;
        }

        $headerColumns = @fgetcsv($csvFile);

        /**
         * we are using uuid, we can't use 1,2,3...
         * timestamp columns
         */
        $headerColumns[] = 'id';
        $headerColumns[] = 'created_at';
        $headerColumns[] = 'updated_at';

        if (array_diff($headerColumns, $columns) !== []) {
            $this->command->line(sprintf('<fg=red>Bro!! May be you forgot to add header columns in <options=bold;fg=red>%s</>', Str::of($filename)->basename()->value()));

            return Command::FAILURE;
        }

        $records = self::generateData($csvFile, $companyId, $headerColumns, $table);

        fclose($csvFile);

        if ($records !== []) {
            return self::storeInDatabase($table, $records);
        }

        return Command::SUCCESS;
    }

    private function renameFile(string $oldFilename): void
    {
        if ($this->command->confirm('Do you want to rename the file name?', true)) {
            $fileBaseName = Str::of($oldFilename)->basename('.csv')->value();
            $changeBaseName = Str::of($fileBaseName)->basename('.csv')->plural()->value();
            $newFileName = Str::replaceFirst($fileBaseName, $changeBaseName, $oldFilename);
            rename($oldFilename, $newFileName);
            $this->command->comment('Okay!! Now you need to re-run the `php artisan db:seed`');
        }
    }

    private function runMigrations(): void
    {
        if ($this->command->confirm('Do you want to run fresh migration?', true)) {
            Artisan::call('migrate:fresh');
            $this->command->comment('Okay!! Now you need to re-run the `php artisan db:seed`');
        }
    }

    private static function generateData($csvFile, ?string $companyId, array $headerColumns, string $table): array
    {
        $records = [];

        while (($data = @fgetcsv($csvFile)) !== false) {

            $fields = [];

            foreach ($headerColumns as $key => $headerColumn) {
                if ($headerColumn === 'password') {
                    $fields[$headerColumn] = $data[$key] !== '' ? bcrypt($data[$key]) : bcrypt('password');

                    continue;
                }

                if ($headerColumn === 'company_id') {
                    $fields[$headerColumn] = $companyId ?? Company::min('id');

                    continue;
                }

                if ($headerColumn === 'id') {
                    $fields[$headerColumn] = Str::orderedUuid();

                    continue;
                }

                if ($headerColumn === 'created_at' || $headerColumn === 'updated_at') {
                    $fields[$headerColumn] = now();

                    continue;
                }

                $fields[$headerColumn] = $data[$key] !== '' ? $data[$key] : null;
            }

            $records[] = $fields;

            // Save memory by storing 1k records, if collected
            if (count($records) > 999) {
                self::storeInDatabase($table, $records);
                $records = [];
            }
        }

        return $records;
    }

    private static function storeInDatabase(string $table, array $records): int
    {
        DB::table($table)->insert($records);

        return Command::SUCCESS;
    }
}
