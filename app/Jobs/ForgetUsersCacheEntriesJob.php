<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Queries\UserQueries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ForgetUsersCacheEntriesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $key,
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = resolve(UserQueries::class)->fetchUsersByLazyCollection();
        $users->each(function (User $user): void {
            Cache::forget($this->key . $user->id);
        });
    }
}
