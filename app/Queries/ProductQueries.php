<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
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
     * @param  array<string, string|array<int, UploadedFile|array<int, array<int, string>>>>  $data
     */
    public function create(array $data): void
    {
        $data['company_id'] ??= app('company_id');

        $product = Product::create($data);

        $this->storeBundleProducts($product, $data);

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
     * @param  array<string, mixed>  $data
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

            $isProductBundle = $product->status;

            $product->update($data);

            if ($product->refresh()->is_bundle !== $isProductBundle) {

                /** @var array<int, mixed> $bundledProducts */
                $bundledProducts = [];

                if ($data['is_bundle']) {
                    /** @var array<string, mixed> $bundleItems */
                    $bundleItems = $data['bundle_items'];

                    foreach ($bundleItems['ids'] as $key => $bundleItemId) {
                        $bundledProducts[] = [
                            'parent_product_id' => $product->id,
                            'child_product_id' => $bundleItemId,
                            'quantity' => $bundleItems['quantities'][$key],
                            'sort_order' => $this->sortOrders($bundleItems, $key),
                        ];
                    }

                    resolve(ProductBundleQueries::class)->upsert($bundledProducts);

                    return;
                }

                resolve(ProductBundleQueries::class)->deleteByParentId($product->id);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storeBundleProducts(Product $product, array $data): void
    {
        if ($product->is_bundle) {
            /** @var array<int, mixed> $bundledProducts */
            $bundledProducts = [];

            /** @var array<string, mixed> $bundleItems */
            $bundleItems = $data['bundle_items'];

            foreach ($bundleItems['ids'] as $key => $bundleItemId) {
                $bundledProducts[] = [
                    'id' => Str::orderedUuid(),
                    'parent_product_id' => $product->id,
                    'child_product_id' => $bundleItemId,
                    'quantity' => $bundleItems['quantities'][$key],
                    'sort_orders' => $this->sortOrders($bundleItems, $key),
                ];
            }

            resolve(ProductBundleQueries::class)->createMany($bundledProducts);
        }
    }

    /**
     * @param  array<string, mixed>  $bundleItems
     */
    private function sortOrders(array $bundleItems, int $key): ?int
    {
        if (! array_key_exists('sort_orders', $bundleItems)) {
            return null;
        }

        if (array_key_exists($key, $bundleItems['sort_orders'])) {
            return (int) $bundleItems['sort_orders'][$key];
        }

        return null;
    }
}
