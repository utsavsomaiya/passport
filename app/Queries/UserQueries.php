<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

class UserQueries
{
    public function findByEmail(string $email): ?User
    {
        return User::query()->firstWhere('email', $email);
    }
}
