<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Company::class, 'company_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }
};
