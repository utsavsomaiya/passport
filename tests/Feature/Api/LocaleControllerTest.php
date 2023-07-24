<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Locale;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithPermissions(
        ['title' => Permission::ability('create', 'locales')],
        ['title' => Permission::ability('update', 'locales')],
        ['title' => Permission::ability('delete', 'locales')],
        ['title' => Permission::ability('fetch', 'locales')]
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
                ->etc()
        );
});

test('it can create locale', function (): void {
    $locale = Locale::factory()->make([
        'company_id' => $this->company->id,
    ]);

    $response = $this->withToken($this->token)->postJson(route('api.locales.create'), [
        'name' => $locale->name,
        'code' => $locale->code,
        'status' => '0',
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
    ]), ['name' => 'Malay', 'code' => 'ms', 'status' => '0']);

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
