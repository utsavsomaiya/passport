<?php

declare(strict_types=1);

use App\Models\Role;
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
        Schema::create('role_user', function (Blueprint $table): void {
            $table->primary(['role_id', 'user_id']);
            $table->foreignIdFor(Role::class, 'role_id')->constrained('roles');
            $table->foreignIdFor(User::class, 'user_id')->constrained('users');
            $table->timestamps();
        });
    }
};
