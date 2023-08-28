<?php

declare(strict_types=1);

use App\Models\Locale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locale_products', function (Blueprint $table): void {
            $table->dropForeignIdFor(Locale::class, 'locale_id');
        });

        Schema::dropIfExists('locales');
    }
};
