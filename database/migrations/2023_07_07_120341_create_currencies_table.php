<?php

declare(strict_types=1);

use App\Enums\CurrencyStatus;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->string('code');
            $table->integer('exchange_rate')->default(0);
            $table->string('format');
            $table->string('decimal_point');
            $table->string('thousand_separator');
            $table->string('decimal_places')->nullable();
            $table->boolean('is_default')->default(false);
            $table->tinyInteger('status')->default(CurrencyStatus::DISABLE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
