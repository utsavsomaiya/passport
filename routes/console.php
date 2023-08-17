<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Laravel\Prompts\Prompt;

// When terminal height is less than 8. It will display old prompt.
Prompt::fallbackWhen(shell_exec('tput lines') < 8);

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
        ->map(fn (string $command) => rtrim($command, ';'))
        ->map(fn (string $sanitizedCommand) => eval("dump({$sanitizedCommand});"))
        ->implode(PHP_EOL);

})->purpose('Run the given code and dump the result');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
