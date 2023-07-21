<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class TemplateQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Template::class, $request)
            ->allowedFilters([$this->filter('name')])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->select('id', 'name', 'description', 'created_at')
            ->where('company_id', app('company_id'))
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        Template::create($data);
    }

    public function delete(string $id): void
    {
        $template = Template::query()->where('id', $id)->withExists('attributes')->firstOrFail();

        // @phpstan-ignore-next-line
        abort_if($template->attributes_exists > 0, Response::HTTP_NOT_ACCEPTABLE, sprintf('The template has attribute. Cannot be deleted %s.', $template->name));

        Template::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param  array<string, string>  $data
     */
    public function update(array $data, string $id): void
    {
        $data['company_id'] ??= app('company_id');

        Template::query()
            ->where('company_id', app('company_id'))
            ->where('id', $id)
            ->update($data);
    }
}
