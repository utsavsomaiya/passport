<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

test('it can fetch all users companies', function (): void {
    $user = User::factory()
        ->hasAttached(Company::factory(2))
        ->create(['password' => bcrypt($password = 'test')]);

    $response = $this->postJson(route('api.companies.fetch'), [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertOk()
        ->assertJson(
            fn (AssertableJson $json): AssertableJson => $json
                ->has(
                    'data',
                    fn (AssertableJson $json): AssertableJson => $json
                        ->has(
                            '0',
                            fn (AssertableJson $json): AssertableJson => $json
                                ->where('id', ($user = $user->companies->sortByDesc('created_at')->first())->id)
                                ->where('name', $user->name)
                                ->where('email', $user->email)
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});
