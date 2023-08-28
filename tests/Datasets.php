<?php

declare(strict_types=1);

dataset('string field validation check', [
    fn (): array|string => fake()->words(6),
    fake()->sentence(256),
]);

dataset('modules', ['users', 'roles', 'currencies', 'hierarchies', 'price_books', 'templates', 'attributes']);
