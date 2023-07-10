<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Models\Company;
use App\Models\Locale;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $user = User::factory()
        ->has(
            Role::factory()
                ->has(Permission::factory(4)->sequence(
                    ['title' => PermissionEnum::LOCALES->can('fetch')],
                    ['title' => PermissionEnum::LOCALES->can('create')],
                    ['title' => PermissionEnum::LOCALES->can('delete')],
                    ['title' => PermissionEnum::LOCALES->can('update')]
                ))
                ->named('Access Manager')
        )
        ->create();

    $this->company = Company::factory()->create();

    $this->token = $user->createToken('test', $this->company->id)->plainTextToken;

    $this->user = Sanctum::actingAs($user);
});

test('it can fetch locales', function (): void {
    $locales = Locale::factory(3)->create([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->getJson(route('api.locales.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($locale = $locales->sortByDesc('id')->first())->id)
                                ->where('name', $locale->name)
                                ->where('code', $locale->code)
                                ->etc()
                        )
                        ->etc()
                )
        );
});

test('it can create locale', function (): void {
    $locale = Locale::factory()->make([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.locales.create'), [
        'name' => $locale->name,
        'code' => $locale->code,
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', 'Locales created successfully.')
        );

    $this->assertDatabaseCount(Locale::class, 1);
});

test('it can delete locale', function (): void {
    $locale = Locale::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->deleteJson(route('api.locales.delete', [
        'id' => $locale->id,
    ]));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', 'Locales deleted successfully.')
        );

    $this->assertModelMissing($locale);
});

test('it can update locale', function (): void {
    $locale = Locale::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.locales.update', [
        'id' => $locale->id,
    ]), ['name' => 'Malay', 'code' => 'ms']);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', 'Locales updated successfully.')
        );

    $this->assertDatabaseHas(Locale::class, [
        'id' => $locale->id,
        'name' => 'Malay',
    ]);
});
