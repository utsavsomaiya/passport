<?php

declare(strict_types=1);

use App\Models\Company;
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
        Schema::create('products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->string('sku')->unique();
            $table->string('upc_ean')->unique();
            $table->string('external_reference')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_bundle')->default(false);
            // `is_manage_inventory` and `stock` are remaining...
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignIdFor(Product::class, 'parent_product_id')
                ->nullable()
                ->after('company_id')
                ->constrained('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
