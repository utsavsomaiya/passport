<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class CleanAuditableCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:clean
                            {--days= : (optional) Records older than this number of days will be cleaned.}
                            {--force : (optional) Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old records from the audits';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $this->comment('Cleaning auditable...');

        $maxAgeInDays = $this->option('days') ?? config('audit.delete_records_older_than_days');

        $cutOffDate = Carbon::now()->subDays($maxAgeInDays)->format('Y-m-d H:i:s');

        $amountDeleted = Audit::query()->where('created_at', '<', $cutOffDate)->delete();

        $this->info(sprintf('Deleted %s record(s) from the audits.', $amountDeleted));

        $this->comment('All done!');

        return self::SUCCESS;
    }
}
