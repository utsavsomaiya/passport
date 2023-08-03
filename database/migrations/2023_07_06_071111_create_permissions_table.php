<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Role::class, 'role_id')->constrained('roles');
            $table->string('title');
            $table->timestamps();
        });
    }
};
