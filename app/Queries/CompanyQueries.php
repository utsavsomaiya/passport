<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyQueries extends GlobalQueries
{
    public function listQuery(Request $request): Collection
    {
        $userCompaniesIds = $request->user()?->companies->pluck('id')->toArray();

        return QueryBuilder::for(Company::class, $request)
            ->allowedFilters([
                $this->filter('name'),
                $this->filter('email'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->select('id', 'name', 'email', 'created_at')
            ->whereIn('id', $userCompaniesIds)
            ->get();
    }
}
