<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Hierarchy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hierarchies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->timestamps();
        });

        Schema::table('hierarchies', function (Blueprint $table): void {
            $table->after('company_id', function (Blueprint $table): void {
                $table->foreignIdFor(Hierarchy::class, 'parent_hierarchy_id')
                    ->nullable()
                    ->constrained('hierarchies');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hierarchies');
    }
};
