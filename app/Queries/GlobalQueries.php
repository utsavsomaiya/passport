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
                        if (count(array_filter($value, 'strlen')) === 0) {
                            return $query;
                        }

                        $query->where(function ($query) use ($value, $wrappedProperty) {
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

            if ($filterMethod[$columnName] === 'like') {
                return AllowedFilter::partial($columnName);
            }
        }

        return AllowedFilter::exact($columnName);
    }

    public function filterWithRelationship(string $columnName, string $relationship): AllowedFilter
    {
        return AllowedFilter::callback($columnName, function (Builder $query, $value, $property) use ($relationship): void {
            if (is_array($value)) {
                $value = implode(',', $value); // Relationship can not support multiple value for now...
            }

            $filterMethod = request()->get('filter_method', []);

            if (array_key_exists($property, $filterMethod)) {
                if ($filterMethod[$property] === 'equals') {
                    $query->whereHas($relationship, function ($query) use ($property, $value) {
                        $query->where($property, '=', $value);
                    });
                }

                if ($filterMethod[$property] === 'starts_with') {
                    $query->whereHas($relationship, function ($query) use ($property, $value) {
                        $query->where($property, 'LIKE', $value . '%');
                    });
                }

                if ($filterMethod[$property] === 'ends_with') {
                    $query->whereHas($relationship, function ($query) use ($property, $value) {
                        $query->where($property, 'LIKE', '%' . $value);
                    });
                }

                if ($filterMethod[$property] === 'is_like') {
                    $query->whereHas($relationship, function ($query) use ($property, $value) {
                        $query->where($property, 'LIKE', '%' . $value . '%');
                    });
                }

                return;
            }

            $query->whereHas($relationship, function ($query) use ($property, $value) {
                $query->where($property, '=', $value);
            });
        });
    }

    public function sortingWithRelationShips(string $relationship): Closure
    {
        return function (Builder $query, bool $descending, string $property) use ($relationship) {
            $query->whereHas($relationship, function ($query) use ($descending, $property) {
                $direction = $descending ? 'DESC' : 'ASC';
                $query->orderBy($property, $direction);
            });
        };
    }

    /**
     * @return array<int, mixed>
     */
    protected function getWhereRawParameters(string $value, string $property): array
    {
        return [
            "{$property} LIKE ?",
            ["%{$value}"],
        ];
    }
}
