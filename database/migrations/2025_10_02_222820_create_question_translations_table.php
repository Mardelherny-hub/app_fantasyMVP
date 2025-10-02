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
        Schema::create('question_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('locale', 5); // es/en/fr
            $table->text('text'); // Enunciado de la pregunta
            $table->timestamps();

            // Una traducción única por pregunta por idioma
            $table->unique(['question_id', 'locale']);
            
            // Índice
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_translations');
    }
};