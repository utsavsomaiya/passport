<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use App\Models\ProductBundle;
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
                        'product' => function ($query): void {
                            $query->with('media:id,file_name,model_id,model_type,collection_name,disk,created_at')
                                ->select('id', 'name', 'description', 'slug', 'sku', 'upc_ean', 'external_reference', 'status', 'is_bundle', 'created_at');
                        },
                    ]);
                },
            ])
            ->jsonPaginate();
    }

    /**
     * @param  array<string, string|array<int, UploadedFile>>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        $product = Product::create($data);

        if ($product->is_bundle) {
            foreach ($data['bundle_items'] as $key => $value) {
            }
        }

        if (array_key_exists('images', $data)) {
            foreach ($data['images'] as $image) {
                $product->addMedia($image)->toMediaCollection('product_images');
            }
        }
    }

    public function delete(string $id): void
    {
        Product::where('id', $id)->delete(); // Soft Delete
    }

    /**
     * @param  array<string, string|array<int, UploadedFile>>  $data
     */
    public function update(string $id, array $data): void
    {
        $product = Product::find($id);

        if ($product) {

            if (array_key_exists('images', $data)) {
                /** @var array<int, UploadedFile> $productImages */
                $productImages = $data['images'];

                if ($productImages !== []) {
                    $product->clearMediaCollection('product_images');

                    foreach ($productImages as $productImage) {
                        $product->addMedia($productImage)->toMediaCollection('product_images');
                    }
                }
            }

            $product->update($data);
        }
    }
}
