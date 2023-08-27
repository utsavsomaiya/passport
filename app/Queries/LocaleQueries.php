<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class LocaleQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Locale::class, $request)
            ->defaultSort('-created_at')
            ->allowedFilters([
                $this->filter('name'),
                $this->filter('code'),
            ])
            ->allowedSorts(['name', 'code', 'created_at'])
            ->select('id', 'name', 'code', 'status')
            ->where('company_id', app('company_id'))
            ->jsonPaginate();
    }
}
