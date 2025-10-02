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
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')
                  ->constrained('quiz_attempts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('selected_option_id')
                  ->nullable() // NULL si no respondió
                  ->constrained('question_options')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->boolean('is_correct')->default(false);
            $table->datetime('answered_at');
            $table->integer('time_taken_ms')->nullable(); // Tiempo en milisegundos
            $table->smallInteger('points_awarded')->default(0);

            // Una respuesta única por attempt por pregunta
            $table->unique(['quiz_attempt_id', 'question_id']);
            
            // Índices
            $table->index('quiz_attempt_id');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
    }
};