<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Locale;
use Illuminate\Pagination\LengthAwarePaginator;

class LocaleQueries
{
    /**
     * @param  array<string, string>  $filterData
     */
    public function listQuery(array $filterData): LengthAwarePaginator
    {
        return Locale::query()
            ->where('company_id', $filterData['company_id'])
            ->when($filterData['sort_by'] && $filterData['sort_direction'], function ($query) use ($filterData): void {
                $query->orderBy($filterData['sort_by'], $filterData['sort_direction']);
            }, function ($query): void {
                $query->orderBy('id', 'desc');
            })
            ->paginate((int) $filterData['per_page']);
    }

    public function delete(string $id): void
    {
        Locale::where('id', $id)->delete();
    }
}
