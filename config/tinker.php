<?php

declare(strict_types=1);

use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Foundation\Console\ConfigClearCommand;
use Illuminate\Support\Benchmark;

return [

    /*
    |--------------------------------------------------------------------------
    | Console Commands
    |--------------------------------------------------------------------------
    |
    | This option allows you to add additional Artisan commands that should
    | be available within the Tinker environment. Once the command is in
    | this array you may execute the command in Tinker using its name.
    |
    */

    'commands' => [
        WipeCommand::class,
        FreshCommand::class,
        SeedCommand::class,
        ConfigCacheCommand::class,
        ConfigClearCommand::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Aliased Classes
    |--------------------------------------------------------------------------
    |
    | Tinker will not automatically alias classes in your vendor namespaces
    | but you may explicitly allow a subset of classes to get aliased by
    | adding the names of each of those classes to the following list.
    |
    */

    'alias' => [
        'Benchmark' => Benchmark::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Classes That Should Not Be Aliased
    |--------------------------------------------------------------------------
    |
    | Typically, Tinker automatically aliases classes as you require them in
    | Tinker. However, you may wish to never alias certain classes, which
    | you may accomplish by listing the classes in the following array.
    |
    */

    'dont_alias' => [
        'App\Nova',
    ],

];
