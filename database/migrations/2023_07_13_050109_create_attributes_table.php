<?php

declare(strict_types=1);

use App\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Template::class, 'template_id')->constrained('templates');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->tinyInteger('field_type')->comment($this->fieldTypeComment());
            $table->string('default_value')->nullable();
            $table->json('validation');
            $table->json('options')->nullable()->comment('If field type is select or list then user gives its options');
            $table->boolean('is_required')->default(false);
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }

    private function fieldTypeComment(): string
    {
        return <<<'HTML'
            1. Radio Button (switch, boolean)
            2. Input (type=number, step=any, float)
            3. Input (type=number, integer)
            4. Input (type=text, string)
            5. Input (type=date, date)
            6. Select (multiple=false, string)
            7. Select (multiple=true, array)
        HTML;
    }
};
