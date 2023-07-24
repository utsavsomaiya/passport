<?php

declare(strict_types=1);

use App\Models\Attribute;
use App\Models\Template;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    [$this->user, $this->company, $this->token] = frontendApiLoginWithUser('Super Admin');
});

test('it give an error when the wrong `template id` is provided.', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch'), [
        'template_id' => fake()->uuid(),
    ]);

    $response->assertOk();
});

test('it give an error when `filter[name]` is ', function ($data): void {
    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch', [
        'filter[name]' => $data,
    ]));

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['filter.name']]);
})->with('string field validation check');

test('it give an error when filter `filter[template_name]` is ', function ($data): void {
    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch', [
        'filter[template_name]' => $data,
    ]));

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['filter.template_name']]);
})->with('string field validation check');

test('it give an error `sort` cannot be a string', function (): void {
    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch', [
        'sort' => fake()->words(),
    ]));

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonStructure(['message', 'errors' => ['sort']]);
});

test('it can fetch attributes', function (): void {
    $attributes = Attribute::factory(2)->create();

    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($attribute = $attributes->sortByDesc('created_at')->first())->id)
                                ->where('name', $attribute->name)
                                ->where('description', $attribute->description)
                                ->where('field_type', $attribute->field_type->resourceName())
                                ->where('field_description', $attribute->field_type->description())
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});

test('it can fetch attributes using template id', function (): void {
    $template = Template::factory()->create();

    Attribute::factory($count = 2)->for($template)->create();

    $response = $this->withToken($this->token)->getJson(route('api.attributes.fetch'), [
        'template_id' => $template->id,
    ]);

    $response->assertOk()->assertJsonCount($count, 'data');
});

test('it can create an attribute', function (): void {
    $template = Template::factory()->for($this->company)->create();

    $response = $this->withToken($this->token)->postJson(route('api.attributes.create'), [
        'name' => 'Brand',
        'template_id' => $template->id,
        'description' => 'This is for choose the brand of the items',
        'field_type' => 'Select',
        'options' => ['Nike', "Levi's", 'Ralph Lauren', 'Tommy Hilfiger', 'Calvin Klein'],
        'is_required' => '1',
        'status' => '1',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    $this->assertDatabaseHas(Attribute::class, [
        'name' => 'Brand',
    ]);
});

test('it can delete an attribute', function (): void {
    $attribute = Attribute::factory()->create();

    $response = $this->withToken($this->token)->deleteJson(route('api.attributes.delete', [
        'id' => $attribute->id,
    ]));

    $response->assertSuccessful()->assertJsonStructure(['success']);
});

test('it can update an attribute', function (): void {
    $template = Template::factory()->for($this->company)->create();

    $attribute = Attribute::factory()->for($template)->create();

    $response = $this->withToken($this->token)->postJson(route('api.attributes.update', [
        'id' => $attribute->id,
    ]), [
        'name' => 'Brand',
        'template_id' => $template->id,
        'description' => 'This is for choose the brand of the items',
        'field_type' => 'Select',
        'options' => $options = ['Nike', "Levi's", 'Ralph Lauren', 'Tommy Hilfiger', 'Calvin Klein'],
        'is_required' => '0',
        'status' => '1',
    ]);

    $response->assertOk()->assertJsonStructure(['success']);

    expect($attribute->refresh())
        ->name->toBe('Brand')
        ->options->toBe($options);
});
