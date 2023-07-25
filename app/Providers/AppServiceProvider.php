<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Http\Client\PendingRequest;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
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

        Response::macro('api', fn (string $message, array $extras = []): JsonResponse => Response::json(['success' => __($message), ...$extras]));

        Http::macro('postman', fn (): PendingRequest => Http::withHeaders(['X-Api-Key' => env('POSTMAN_API_KEY')])->baseUrl(env('POSTMAN_URL')));
    }
}
