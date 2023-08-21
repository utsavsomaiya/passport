<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ProductQueries extends GlobalQueries
{
    /**
     * @return array<int, string>
     */
    public function selectedColumns(): array
    {
        return ['id', 'name', 'description', 'slug', 'sku', 'upc_ean', 'external_reference', 'status', 'is_bundle', 'created_at'];
    }

    public function listQuery(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class, $request)
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at', 'sku'])
            ->allowedFilters([$this->filter('name'), $this->filter('sku'), $this->filter('upc_ean'), 'is_bundle', 'status'])
            ->where('company_id', app('company_id'))
            ->select($this->selectedColumns())
            ->with([
                'media:id,file_name,model_id,model_type,collection_name,disk,created_at',
                'bundleComponents' => function (HasMany $relation): void {
                    $relation->with('childProduct', function (BelongsTo $relation): void {
                        $relation->with('media:id,file_name,model_id,model_type,collection_name,disk,created_at')
                            ->select($this->selectedColumns());
                    });
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

    /**
     * @throws ModelNotFoundException<Product>
     */
    public function fetchProduct(string $id): Product
    {
        return Product::query()
            ->select('id')
            ->where('company_id', app('company_id'))
            ->findOrFail($id);
    }

    public function unbundleProduct(string $productId): void
    {
        Product::query()
            ->where('id', $productId)
            ->update(['is_bundle' => false]);
    }
}
