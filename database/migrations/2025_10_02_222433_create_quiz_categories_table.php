<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Base (solo clave técnica)
        Schema::create('quiz_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // p.ej. rules, history, players, tournaments
            $table->timestamps();
        });

        // Traducciones (textos por idioma)
        Schema::create('quiz_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_category_id')
                ->constrained('quiz_categories')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('locale', 5); // es, en, fr
            $table->string('name');
            $table->timestamps();

            $table->unique(['quiz_category_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_category_translations');
        Schema::dropIfExists('quiz_categories');
    }
};
