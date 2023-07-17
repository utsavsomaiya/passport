<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserQueries extends GlobalQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFields(['first_name', 'last_name', 'username', 'email', 'created_at'])
            ->allowedFilters([
                AllowedFilter::callback('name', function (Builder $query, $value) {
                    $name = explode(' ', $value, 2);
                    $query->where('first_name', 'LIKE', '%' . $name[0] . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $name[0] . '%');
                }),
                $this->filter('username'),
                $this->filter('email'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['first_name', 'last_name', 'created_at'])
            ->mergeSelect('id')
            ->with([
                'tokens:id,tokenable_id,tokenable_type,last_used_at',
                'roles:id,name',
            ])
            ->jsonPaginate();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->firstWhere('email', $email);
    }
}
