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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')
                  ->constrained('quizzes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('question_id')
                  ->constrained('questions')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->tinyInteger('order'); // Orden de presentación

            // Una pregunta única por quiz
            $table->unique(['quiz_id', 'question_id']);
            
            // Índices
            $table->index('quiz_id');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};