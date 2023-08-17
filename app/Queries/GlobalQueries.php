<?php

declare(strict_types=1);

namespace App\Queries;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class GlobalQueries
{
    public function filter(string $columnName): AllowedFilter
    {
        $filterMethod = request()->get('filter_method', []);

        if (array_key_exists($columnName, $filterMethod)) {
            if ($filterMethod[$columnName] === 'equals') {
                return AllowedFilter::exact($columnName);
            }

            if ($filterMethod[$columnName] === 'starts_with') {
                return AllowedFilter::beginsWithStrict($columnName);
            }

            if ($filterMethod[$columnName] === 'ends_with') {
                return AllowedFilter::callback($columnName, function (Builder $query, $value, $property) {
                    $wrappedProperty = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($property));

                    if (is_array($value)) {
                        if (array_filter($value, 'strlen') === []) {
                            return $query;
                        }

                        $query->where(function ($query) use ($value, $wrappedProperty): void {
                            foreach (array_filter($value, 'strlen') as $partialValue) {
                                [$sql, $bindings] = $this->getWhereRawParameters($partialValue, $wrappedProperty);
                                $query->orWhereRaw($sql, $bindings);
                            }
                        });

                        return;
                    }

                    [$sql, $bindings] = $this->getWhereRawParameters($value, $wrappedProperty);
                    $query->whereRaw($sql, $bindings);
                });
            }

            if ($filterMethod[$columnName] === 'is_like') {
                return AllowedFilter::partial($columnName);
            }
        }

        return AllowedFilter::exact($columnName);
    }

    public function filterWithRelationship(string $columnName, string $relationship): AllowedFilter
    {
        return AllowedFilter::callback($columnName, function (Builder $query, $value, $property) use ($relationship): void {
            if (is_array($value)) {
                $value = $value[0]; // Relationship can not support multiple value for now...
            }

            $filterMethod = request()->get('filter_method', []);

            if (array_key_exists($property, $filterMethod)) {
                if ($filterMethod[$property] === 'equals') {
                    $query->whereHas($relationship, function ($query) use ($property, $value): void {
                        $query->where($property, '=', $value);
                    });
                }

                if ($filterMethod[$property] === 'starts_with') {
                    $query->whereHas($relationship, function ($query) use ($property, $value): void {
                        $query->where($property, 'LIKE', $value . '%');
                    });
                }

                if ($filterMethod[$property] === 'ends_with') {
                    $query->whereHas($relationship, function ($query) use ($property, $value): void {
                        $query->where($property, 'LIKE', '%' . $value);
                    });
                }

                if ($filterMethod[$property] === 'is_like') {
                    $query->whereHas($relationship, function ($query) use ($property, $value): void {
                        $query->where($property, 'LIKE', '%' . $value . '%');
                    });
                }

                return;
            }

            $query->whereHas($relationship, function ($query) use ($property, $value): void {
                $query->where($property, '=', $value);
            });
        });
    }

    /**
     * @return array<int, mixed>
     */
    protected function getWhereRawParameters(string $value, string $property): array
    {
        return [
            sprintf('%s LIKE ?', $property),
            [sprintf('%%%s', $value)], // if $value is 'pxm', it will return '%pxm'
        ];
    }
}
