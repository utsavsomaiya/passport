<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Locale;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class LocaleQueries extends GlobalQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Locale::class)
            ->allowedFields(['name', 'code', 'status'])
            ->defaultSort('-created_at')
            ->allowedFilters([
                $this->filter('name'),
                $this->filter('code'),
            ])
            ->allowedSorts(['name', 'code', 'created_at'])
            ->where('company_id', app('company_id'))
            ->mergeSelect('id')
            ->jsonPaginate();
    }

    public function delete(string $id): void
    {
        Locale::where('id', $id)
            ->where('company_id', app('company_id'))
            ->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        Locale::create($data);
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(string $id, array $data): void
    {
        Locale::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
