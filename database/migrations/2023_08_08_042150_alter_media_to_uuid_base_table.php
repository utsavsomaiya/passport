<?php

declare(strict_types=1);

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropPrimary();
            $table->uuid('id')->primary()->change();
        });

        Media::cursor()->each(function (Media $media): void {
            $media->id = Str::orderedUuid();
            $media->save();
        });
    }
};
