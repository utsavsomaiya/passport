<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class ProductMediaQueries
{
    /**
     * @throws ModelNotFoundException<Product>
     */
    public function listQuery(string $productId): MediaCollection
    {
        $product = Product::query()
            ->where('company_id', app('company_id'))
            ->findOrFail($productId);

        return $product->getMedia('product_images');
    }

    public function create(Request $request, string $productId): void
    {
        $product = Product::query()
            ->where('company_id', app('company_id'))
            ->findOrFail($productId);

        if ($request->has('image_url')) {
            $product->addMediaFromUrl($request->get('image_url'))->toMediaCollection('product_images');
        }

        if ($request->hasFile('images')) {
            $product->addMediaFromRequest('images')->toMediaCollection('product_images');
        }
    }

    public function delete(string $productId, string $id): void
    {
        $product = Product::query()
            ->where('company_id', app('company_id'))
            ->findOrFail($productId);

        $product->deleteMedia($id);
    }
}
