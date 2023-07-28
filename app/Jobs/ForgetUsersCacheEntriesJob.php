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
use Illuminate\Support\Str;

class ForgetUsersCacheEntriesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ?string $user = null,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->user && Str::isUuid($this->user)) {
            $user = resolve(UserQueries::class)->findByIdAndLoadRolesAndPermissions($this->user);
            if ($user instanceof User) {
                FlushCacheJob::dispatch('roles_and_permissions_of_user_' . $user->id);
            }

            return;
        }

        $users = resolve(UserQueries::class)->fetchUsersByLazyCollection();
        $users->each(function (User $user): void {
            FlushCacheJob::dispatch('roles_and_permissions_of_user_' . $user->id);
        });
    }
}
