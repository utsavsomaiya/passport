<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttributeQueries extends GlobalQueries
{
    public function listQuery(?string $templateId): LengthAwarePaginator
    {
        return QueryBuilder::for(Attribute::class)
            ->allowedFields(['id', 'template_id', 'name', 'field_type', 'options', 'is_required', 'status'])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name'])
            ->allowedFilters([
                $this->filter('name'),
                AllowedFilter::callback('template_name', function (Builder $query, $value): void {
                    $query->whereHas('template', function (Builder $query) use($value): void {
                        $query->where('name', $value);
                    });
                }),
                AllowedFilter::callback('options', function (Builder $query, $value): void {
                    $query->whereJsonContains('options', $value);
                }),
            ])
            ->with('template:id,name')
            ->when($templateId, function ($query) use ($templateId): void {
                $query->where('template_id', $templateId);
            })
            ->jsonPaginate();
    }

    /**
     * @param  array<int, string|array<int, string>>  $data
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
     * @param  array<int, string|array<int, string>>  $data
     */
    public function update(array $data, string $id): void
    {
        Attribute::query()
            ->where('id', $id)
            ->update($data);
    }
}
