<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Company;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyQueries extends GlobalQueries
{
    public function listQuery(User $user): LengthAwarePaginator
    {
        $userCompaniesIds = $user->companies->pluck('id')->toArray();

        return QueryBuilder::for(Company::class)
            ->allowedFilters([
                $this->filter('name'),
                $this->filter('email'),
            ])
            ->allowedFields(['name', 'email', 'created_at'])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->mergeSelect('id')
            ->whereIn('id', $userCompaniesIds)
            ->jsonPaginate();
    }
}
