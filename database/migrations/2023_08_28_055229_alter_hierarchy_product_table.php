<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hierarchy_product', function (Blueprint $table) {
            $table->renameColumn('is_curated_product', 'is_curated');
        });
    }
};
