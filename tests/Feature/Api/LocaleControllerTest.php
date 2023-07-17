<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Models\Locale;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithPermissions(
        ['title' => PermissionEnum::LOCALES->can('create')],
        ['title' => PermissionEnum::LOCALES->can('update')],
        ['title' => PermissionEnum::LOCALES->can('delete')],
        ['title' => PermissionEnum::LOCALES->can('fetch')]
    );
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
                                ->where('id', ($locale = $locales->sortByDesc('created_at')->first())->id)
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
                ->where('success', __('Locale created successfully.'))
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
                ->where('success', __('Locale deleted successfully.'))
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
                ->where('success', __('Locale updated successfully.'))
        );

    $this->assertDatabaseHas(Locale::class, [
        'id' => $locale->id,
        'name' => 'Malay',
    ]);
});
