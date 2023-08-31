<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Facades\Laravel\Passport\PersonalAccessTokenFactory;
use Illuminate\Support\Arr;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessClient;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function passportLogin()
{
    $user = User::factory()->create();
    $client = Client::factory()->state(fn (): array => ['personal_access_client' => true])->create(['user_id' => $user->id]);
    PersonalAccessClient::create(['client_id' => $client->id]);

    return [PersonalAccessTokenFactory::make($user->id, 'test'), $user, $client];
}

function generatePersonalAccessClient(): void
{
    $client = Client::factory()->state(fn (): array => ['personal_access_client' => true])->create();
    PersonalAccessClient::create(['client_id' => $client->id]);
}

function frontendApiLoginWithPermissions(...$permissions)
{
    generatePersonalAccessClient();

    $company = Company::factory()->create();

    $user = User::factory()
        ->has(
            Role::factory()
                ->for($company)
                ->has(Permission::factory(count(Arr::flatten($permissions)))->sequence(...$permissions))
                ->named('Access Manager')
        )
        ->create();

    $token = $user->createToken('test')->accessToken;

    $personalAccessToken = PersonalAccessTokenFactory::findAccessToken(['access_token' => $token]);

    $personalAccessToken->company_id = $company->id;

    $personalAccessToken->save();

    $user = Passport::actingAs($user);

    return [$user, $company, $token];
}

function frontendApiLoginWithUser(string $roleName)
{
    generatePersonalAccessClient();

    $company = Company::factory()->create();

    $user = User::factory()
        ->has(Role::factory()->for($company)->named($roleName))
        ->create();

    $token = $user->createToken('test')->accessToken;

    $personalAccessToken = PersonalAccessTokenFactory::findAccessToken(['access_token' => $token]);

    $personalAccessToken->company_id = $company->id;

    $personalAccessToken->save();

    $user = Passport::actingAs($user);

    return [$user, $company, $token];
}

function getRoutes(array $routes)
{
    $actions = [];

    foreach ($routes as $key => $route) {
        if (is_array($route)) {
            $actions[] = getRoutesAndMethod($key, $route['parameters'], $route['rename']); // Code refactor about the parameters is remaining...

            continue;
        }

        $actions[] = getRoutesAndMethod($route);
    }

    return collect($actions)->flatten()->toArray();
}

function getRoutesAndMethod(string $requestFor, array $parameters = [], array $rename = []): array
{
    return collect(['fetch', 'create', 'update', 'delete'])
        ->map(fn (string $action) => $rename !== [] && array_key_exists($action, $rename) ? $rename[$action] : $action)
        ->filter(fn (?string $action): bool => ! blank($action))
        ->map(fn (string $action) => match ($action) {
            $rename['fetch'] ?? 'fetch' => fn (): array => ['method' => 'get', 'route' => route(sprintf('api.%s.%s', $requestFor, $action), $parameters)],
            $rename['create'] ?? 'create' => fn (): array => ['method' => 'post', 'route' => route(sprintf('api.%s.%s', $requestFor, $action), $parameters)],
            $rename['update'] ?? 'update' => fn (): array => ['method' => 'post', 'route' => route(sprintf('api.%s.%s', $requestFor, $action), ['id' => fake()->uuid()])],
            $rename['delete'] ?? 'delete' => fn (): array => ['method' => 'delete', 'route' => route(sprintf('api.%s.%s', $requestFor, $action), blank($parameters) ? ['id' => fake()->uuid()] : $parameters)],
        })
        ->toArray();
}
