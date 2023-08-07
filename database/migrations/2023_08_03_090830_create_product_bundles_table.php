<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_bundles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Product::class, 'parent_product_id')->constrained('products');
            $table->foreignIdFor(Product::class, 'child_product_id')->constrained('products');
            $table->integer('quantity');
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }
};
