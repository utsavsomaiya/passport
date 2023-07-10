<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Currency;
use Illuminate\Pagination\LengthAwarePaginator;

class CurrencyQueries
{
    /**
     * @param  array<string, string>  $filterData
     */
    public function listQuery(array $filterData): LengthAwarePaginator
    {
        return Currency::query()
            ->select('id', 'code', 'exchange_rate', 'format', 'decimal_places', 'decimal_point', 'thousand_separator', 'is_default', 'status')
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
        Currency::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        Currency::create($data);
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(string $id, array $data): void
    {
        Currency::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
