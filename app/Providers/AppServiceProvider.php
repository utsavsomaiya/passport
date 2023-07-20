<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Model::preventLazyLoading(! $this->app->isProduction());

        Carbon::macro('displayFormat', fn () => $this->format('d F Y, h:i A')); // @phpstan-ignore-line

        Password::defaults(
            fn (): Password => Password::min(8) // Required at least 8 characters...
                ->letters() // ...with at least one letter...
                ->numbers() // Required at least one Number ...
                ->symbols() // Required at least one symbol...
                ->uncompromised() // Must not compromised in data leaks...
        );
    }
}
