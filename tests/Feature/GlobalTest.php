<?php

declare(strict_types=1);

test('all migrations should have not use down method', function (): void {
    $migrationFiles = array_values(array_diff(scandir(database_path('migrations')), ['..', '.']));

    foreach ($migrationFiles as $migrationFile) {
        $migrationInstance = require database_path('migrations/' . $migrationFile);
        $this->assertFalse(method_exists($migrationInstance, 'down'));
    }
});
