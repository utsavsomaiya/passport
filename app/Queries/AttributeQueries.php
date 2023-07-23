<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttributeQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Attribute::class, $request)
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'is_required', 'order', 'created_at'])
            ->allowedFilters([
                $this->filter('name'),
                AllowedFilter::callback('template_name', function (Builder $query, string $value): void {
                    $query->whereHas('template', function (Builder $query) use ($value): void {
                        $query->where('name', $value);
                    });
                }),
                AllowedFilter::callback('options', function (Builder $query, array $value): void {
                    $query->whereJsonContains('options', $value);
                }),
            ])
            ->select('id', 'template_id', 'name', 'description', 'from', 'to', 'field_type', 'options', 'is_required', 'status', 'order', 'created_at')
            ->with('template:id,name')
            ->when($request->template_id, function (Builder $query) use ($request): void {
                $query->where('template_id', $request->template_id);
            })
            ->jsonPaginate();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): void
    {
        Attribute::create($data);
    }

    public function delete(string $id): void
    {
        Attribute::query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data, string $id): void
    {
        Attribute::query()
            ->where('id', $id)
            ->update($data);
    }
}
