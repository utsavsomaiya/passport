<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table): void {
            $table->primary(['company_id', 'user_id']);
            $table->foreignIdFor(Company::class, 'company_id')->constrained('companies');
            $table->foreignIdFor(User::class, 'user_id')->constrained('users');
            $table->timestamps();
        });
    }
};
