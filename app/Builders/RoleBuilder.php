<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModelClass of Role
 *
 * @extends Builder<TModelClass>
 */
class RoleBuilder extends Builder
{
    // This is just for test now...
}
