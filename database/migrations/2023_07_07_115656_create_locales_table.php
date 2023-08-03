<?php

declare(strict_types=1);

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
        Schema::create('locales', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }
};
