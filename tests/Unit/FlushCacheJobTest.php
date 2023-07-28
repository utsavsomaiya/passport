<?php

declare(strict_types=1);

use App\Jobs\FlushCacheJob;
use Illuminate\Support\Facades\Cache;

test('it can flush cache of the given key if it is available in cache.', function (): void {
    Cache::put('test', fake()->words(6));

    FlushCacheJob::dispatch('test');

    expect(Cache::get('test'))->toBeNull();
});
