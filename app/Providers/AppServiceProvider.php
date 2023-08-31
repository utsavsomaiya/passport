<?php

declare(strict_types=1);

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Spatie\QueryBuilder\QueryBuilderRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->runningInConsole() && ! request()->expectsJson()) {
            abort(HttpResponse::HTTP_FORBIDDEN, 'Accept JSON header is missing in the request.');
        }

        QueryBuilderRequest::setArrayValueDelimiter('|'); // By default value is `,`

        Model::preventLazyLoading(! $this->app->isProduction());

        Carbon::macro('displayFormat', fn () => $this->format('d F Y, h:i A')); // @phpstan-ignore-line

        Password::defaults(
            fn (): Password => Password::min(8) // Required at least 8 characters...
                ->letters() // ...with at least one letter...
                ->numbers() // Required at least one Number ...
                ->symbols() // Required at least one symbol...
                ->uncompromised() // Must not compromised in data leaks...
        );

        File::defaults(
            fn (): File => File::image()
                ->types(['jpeg', 'jpg', 'gif', 'png', 'webp'])
                ->max('8mb')
        );

        Response::macro('api', fn (string $message, array $extras = []): JsonResponse => Response::json(['success' => __($message), ...$extras]));

        Http::macro('postman', fn (): PendingRequest => Http::withHeaders(['X-Api-Key' => env('POSTMAN_API_KEY')])->baseUrl(env('POSTMAN_URL')));
    }
}
