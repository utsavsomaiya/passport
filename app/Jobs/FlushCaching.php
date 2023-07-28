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
use Illuminate\Support\Str;

class FlushCaching implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ?string $key = null,
        protected mixed $user = null,
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->key !== null && Cache::has($this->key)) {
            Cache::forget($this->key);

            return;
        }

        if ($this->user instanceof User) {
            $this->user->load('roles');
            $this->user->roles->load('permissions');
            $this->forgetCache($this->user);

            return;
        }

        if (Str::isUuid($this->user)) {
            $user = resolve(UserQueries::class)->findByIdAndLoadRolesAndPermissions($this->user);
            if ($user instanceof User) {
                $this->forgetCache($user);
            }

            return;
        }

        $users = resolve(UserQueries::class)->fetchUsersByLazyCollection();
        $users->each(function (User $user): void {
            $this->forgetCache($user);
        });
    }

    private function forgetCache(User $user): void
    {
        if (Cache::has('roles_and_permissions_of_user_' . $user->id)) {
            Cache::forget('roles_and_permissions_of_user_' . $user->id);
        }
    }
}
