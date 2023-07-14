<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\PriceBook;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class PriceBookQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(PriceBook::class)
            ->select(['id', 'name', 'description'])
            ->defaultSort('-id')
            ->allowedSorts(['name'])
            ->where('company_id', app('company_id'))
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        PriceBook::create($data);
    }

    public function delete(string $id): void
    {
        PriceBook::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        $data['company_id'] ??= app('company_id');

        PriceBook::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
