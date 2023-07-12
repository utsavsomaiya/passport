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
            if (request()->input('filter_action')[$columnName] === 'is') {
                $query->where($columnName, '=', $value);
            }

            if (request()->input('filter_action')[$columnName] === 'starts_with') {
                $query->where($columnName, 'LIKE', $value . '%');
            }

            if (request()->input('filter_action')[$columnName] === 'ends_with') {
                $query->where($columnName, 'LIKE', '%' . $value);
            }

            if (request()->input('filter_action')[$columnName] === 'is_like') {
                $query->where($columnName, 'LIKE', '%' . $value . '%');
            }
        });
    }
}
