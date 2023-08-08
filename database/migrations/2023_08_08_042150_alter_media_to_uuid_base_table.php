<?php

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropPrimary();
            $table->uuid('id')->primary()->change();
        });

        Media::cursor()->each(function (Media $media) {
            $media->id = Str::orderedUuid();
            $media->save();
        });
    }
};
