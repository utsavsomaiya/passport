<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function (): void {
    $loginData = frontendApiLoginWithUser('Super Admin');
    $this->token = $loginData[2];
    $this->user = $loginData[0];
});

test('it can fetch all users companies', function (): void {
    Company::factory(2)->hasAttached($this->user)->create();

    $response = $this->withToken($this->token)->postJson(route('api.companies.fetch'));

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($user = $this->user->companies->sortByDesc('created_at')->first())->id)
                                ->where('name', $user->name)
                                ->where('email', $user->email)
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});

test('it can set the `company_id` into the request bearer token', function (): void {
    $response = $this->withToken($this->token)->postJson(route('api.companies.set'), [
        'company_id' => ($company = Company::factory()->create())->id,
    ]);

    $response->assertOk()->assertJsonStructure(['success']);
});
