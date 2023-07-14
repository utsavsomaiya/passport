<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class GlobalQueries
{
    public function filter(string $columnName): AllowedFilter
    {
        return AllowedFilter::callback($columnName, function (Builder $query, $value) use ($columnName): void {
            $filterMethod = request()->get('filter_method', []);

            if (array_key_exists($columnName, $filterMethod)) {
                if ($filterMethod[$columnName] === 'equals') {
                    $query->where($columnName, '=', $value);
                }

                if ($filterMethod[$columnName] === 'starts_with') {
                    $query->where($columnName, 'LIKE', $value . '%');
                }

                if ($filterMethod[$columnName] === 'ends_with') {
                    $query->where($columnName, 'LIKE', '%' . $value);
                }

                if ($filterMethod[$columnName] === 'is_like') {
                    $query->where($columnName, 'LIKE', '%' . $value . '%');
                }
            }

            $query->where($columnName, '=', $value);
        });
    }
}
