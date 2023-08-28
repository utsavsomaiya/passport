<?php

declare(strict_types=1);

use App\Models\Locale;
use Facades\App\Enums\Permission;
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
    $locales = Locale::factory(3)->for($this->company)->create();

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
