<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Currency;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class CurrencyQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Currency::class)
            ->allowedFields(['id', 'code', 'exchange_rate', 'format', 'decimal_places', 'decimal_point', 'thousand_separator', 'is_default', 'status'])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'format', 'code'])
            ->where('company_id', app('company_id'))
            ->jsonPaginate();
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
