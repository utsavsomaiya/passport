<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Locale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Locale>
 */
class LocaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => fn (): Collection|Model => Company::factory()->create(),
            'name' => fake()->locale(),
            'code' => fake()->languageCode(),
        ];
    }
}
