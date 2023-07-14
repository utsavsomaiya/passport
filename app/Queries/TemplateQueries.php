<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Template;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class TemplateQueries
{
    public function listQuery(): LengthAwarePaginator
    {
        return QueryBuilder::for(Template::class)
            ->allowedFields(['id', 'name', 'description'])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name'])
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
