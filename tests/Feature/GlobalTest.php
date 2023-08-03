<?php

declare(strict_types=1);

test('all migrations should have not use down method', function (): void {
    $migrationPath = database_path('migrations');
    $migrationFiles = scandir($migrationPath);

    foreach ($migrationFiles as $file) {
        if (strpos($file, '.php') !== false) {
            $migrationInstance = require database_path('migrations/' . $file);

            $this->assertFalse(method_exists($migrationInstance, 'down'));
        }
    }
});
