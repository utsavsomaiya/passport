<?php

declare(strict_types=1);

namespace App\Queries\Spatie;

use Illuminate\Database\Query\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ChildProductSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->whereHas('childProduct', function ($query) use ($property, $direction) {
            $query->orderBy($property, $direction);
        });
    }
}
