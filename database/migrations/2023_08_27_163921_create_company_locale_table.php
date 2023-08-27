<?php

declare(strict_types=1);

use App\Models\Company;
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
        Schema::create('company_locale', function (Blueprint $table): void {
            $table->primary(['company_id', 'locale_id']);
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->foreignIdFor(Locale::class, 'locale_id')->constrained('locales');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }
};
