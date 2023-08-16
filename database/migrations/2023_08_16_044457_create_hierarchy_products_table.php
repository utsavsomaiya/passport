<?php

declare(strict_types=1);

use App\Models\Hierarchy;
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
        Schema::create('hierarchy_product', function (Blueprint $table): void {
            $table->primary(['hierarchy_id', 'product_id']);
            $table->foreignIdFor(Hierarchy::class, 'hierarchy_id')->constrained();
            $table->foreignIdFor(Product::class, 'product_id')->constrained();
            $table->timestamps();
        });
    }
};
