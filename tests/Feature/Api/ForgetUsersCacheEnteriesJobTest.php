<?php

declare(strict_types=1);

use App\Jobs\ForgetUsersCacheEntriesJob;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('it can flush users cache entries', function (): void {
    $users = User::factory(10)->create();

    Cache::putMany(array_combine($users->pluck('id')->map(fn ($id): string => 'roles_and_permissions_of_user_' . $id)->toArray(), fake()->words(10)));

    (new ForgetUsersCacheEntriesJob('roles_and_permissions_of_user_'))->handle();

    expect(Cache::get('roles_and_permissions_of_user_' . $users->random()->id))->toBeNull();
});
