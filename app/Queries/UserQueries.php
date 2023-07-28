<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Spatie\QueryBuilder\QueryBuilder;

class UserQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class, $request)
            ->allowedFilters([
                $this->filter('first_name'),
                $this->filter('last_name'),
                $this->filter('username'),
                $this->filter('email'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['first_name', 'last_name', 'created_at'])
            ->select('id', 'first_name', 'last_name', 'username', 'email', 'created_at')
            ->with('roles:id,name')
            ->when($request->role_id, function ($query) use ($request): void {
                $query->whereHas('roles', function ($query) use ($request): void {
                    $query->where('role_id', $request->role_id);
                });
            })
            ->jsonPaginate();
    }

    public function delete(string $id): void
    {
        User::query()->where('id', $id)->delete(); // Soft delete
    }

    public function restore(string $id): void
    {
        User::withTrashed()->where('id', $id)->restore();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        User::create($data);
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        User::query()
            ->where('id', $id)
            ->update($data);
    }

    public function changePassword(Request $request): void
    {
        /** @var User $user */
        $user = $request->user();

        $user->password = bcrypt($request->new_password);
        $user->save();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->firstWhere('email', $email);
    }

    public function findByIdAndLoadRolesAndPermissions(string $id): ?User
    {
        return User::query()
            ->select('id')
            ->with(['roles:id,name', 'roles.permissions:id,role_id,title'])
            ->find($id);
    }

    public function fetchUsersByLazyCollection(): LazyCollection
    {
        return User::query()
            ->select('id')
            ->with(['roles:id,name', 'roles.permissions:id,role_id,title'])
            ->cursor();
    }
}
