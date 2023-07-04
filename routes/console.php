<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('dd {code*}', function () {
    $isAllowedToRun = function () {
        if (env('ALLOW_DD_COMMAND') === true) {
            return true;
        }

        return app()->environment('local');
    };

    if (! $isAllowedToRun()) {
        $this->error('This command can only run if the environment variable `ALLOW_DD_COMMAND` is set to `true` or in local environment');

        return;
    }

    return collect($this->argument('code'))
        ->map(function (string $command) {
            return rtrim($command, ';');
        })
        ->map(function (string $sanitizedCommand) {
            return eval("dump({$sanitizedCommand});");
        })
        ->implode(PHP_EOL);

})->purpose('Run the given code and dump the result');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
