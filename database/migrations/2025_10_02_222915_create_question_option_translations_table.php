<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_option_id')
                  ->constrained('question_options')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('locale', 5); // es/en/fr
            $table->text('text'); // Texto de la opción
            $table->timestamps();

            // Una traducción única por opción por idioma
            $table->unique(['question_option_id', 'locale']);
            
            // Índice
            $table->index('question_option_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_option_translations');
    }
};