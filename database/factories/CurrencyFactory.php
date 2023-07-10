<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CurrencyStatus;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ref: https://useast.cm.elasticpath.com/settings/currencies/35465caf-4446-4c57-9552-f2995cf7558f
        return [
            'company_id' => fn () => Company::factory()->create(),
            'code' => fake()->currencyCode(),
            'format' => '${price}',
            'thousand_seprator' => ',',
            'decimal_places' => '2',
            'is_default' => false,
            'status' => fake()->randomElement(CurrencyStatus::values()),
        ];
    }
}
