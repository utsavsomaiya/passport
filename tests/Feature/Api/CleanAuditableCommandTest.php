<?php

declare(strict_types=1);

use App\Models\Audit;

test('it can clean auditable entries after 30 days', function (): void {
    $oneEightyOneDays = Audit::create([
        'auditable_type' => 'App/Models/User',
        'auditable_id' => fake()->uuid(),
        'event' => fake()->word(),
        'old_values' => [],
        'new_values' => [],
        'url' => fake()->url(),
        'created_at' => now()->subDays(181),
    ]);

    $oneSeventyNineDays = Audit::create([
        'auditable_type' => 'App/Models/User',
        'auditable_id' => fake()->uuid(),
        'event' => fake()->word(),
        'old_values' => [],
        'new_values' => [],
        'url' => fake()->url(),
        'created_at' => now()->subDays(179),
    ]);

    $this->artisan('audit:clean')
        ->assertExitCode(0)
        ->assertSuccessful();

    $this->assertModelMissing($oneEightyOneDays);

    $this->assertModelExists($oneSeventyNineDays);
});