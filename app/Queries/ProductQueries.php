<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ProductQueries extends GlobalQueries
{
    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class, $request)
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at', 'sku'])
            ->allowedFilters([$this->filter('name'), $this->filter('sku'), $this->filter('upc_ean'), 'is_bundle'])
            ->where('company_id', app('company_id'))
            ->select('id', 'name', 'description', 'slug', 'sku', 'upc_ean', 'external_reference', 'status', 'is_bundle', 'created_at')
            ->with([
                'media:id,file_name,model_id,model_type,collection_name,disk,created_at',
                'productBundles' => function ($query): void {
                    $query->with([
                        'childProduct' => function ($query): void {
                            $query->with('media:id,file_name,model_id,model_type,collection_name,disk,created_at')
                                ->select('id', 'name', 'description', 'slug', 'sku', 'upc_ean', 'external_reference', 'status', 'is_bundle', 'created_at');
                        },
                    ]);
                },
            ])
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string|UploadedFile>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        $product = Product::create($data);

        if (array_key_exists('image', $data)) {
            $product->addMedia($data['image'])->toMediaCollection('product_images');
        }
    }

    public function delete(string $id): void
    {
        Product::where('id', $id)->delete(); // Soft Delete
    }

    /**
     * @param  array<string, string|UploadedFile>  $data
     */
    public function update(string $id, array $data): void
    {
        $product = Product::query()
            ->where('company_id', app('company_id'))
            ->findOrFail($id);

        $product->clearMediaCollection('product_images');

        if (array_key_exists('image', $data)) {
            $product->addMedia($data['image'])->toMediaCollection('product_images');
        }

        $product->update($data);
    }
}
