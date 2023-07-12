<?php

declare(strict_types=1);

use App\Models\Template;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it can fetch templates', function (): void {
    $template = Template::factory()->for($this->company)->create(['name' => 'Fashion']);

    $response = $this->withToken($this->token)->getJson(route('api.templates.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', $template->id)
                                ->where('name', $template->name)
                                ->etc()
                        )
                        ->etc()
                )
        );
});

test('it can create a template', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.templates.create'), [
        'name' => $name = 'Fashion',
        'description' => 'This template is only for fashions',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Template created successfully.'))
        );

    $this->assertDatabaseHas(Template::class, [
        'name' => $name,
    ]);
});

test('it can delete a price book', function (): void {
    $template = Template::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.templates.delete', [
        'id' => $template->id,
    ]));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Template deleted successfully.'))
        );

    $this->assertModelMissing($template);
});

test('it can update a template', function (): void {
    $template = Template::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.templates.update', [
        'id' => $template->id,
    ]), [
        'name' => $name = 'Fashion',
        'description' => 'This is for only fashion',
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->where('success', __('Template updated successfully.'))
        );

    $this->assertDatabaseHas(Template::class, [
        'id' => $template->id,
        'name' => $name,
    ]);
});
