<?php

declare(strict_types=1);

use App\Models\Locale;
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
        Schema::create('locale_products', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Locale::class, 'locale_id')->constrained();
            $table->foreignIdFor(Product::class, 'product_id')->constrained();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
};
