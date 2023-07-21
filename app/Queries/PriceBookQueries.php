<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\PriceBook;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class PriceBookQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(PriceBook::class, $request)
            ->allowedFilters([
                $this->filter('name'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->select('id', 'name', 'description', 'created_at')
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
