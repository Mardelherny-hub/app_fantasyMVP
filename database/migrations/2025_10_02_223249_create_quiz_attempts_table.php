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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')
                  ->constrained('quizzes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->datetime('started_at');
            $table->datetime('finished_at')->nullable();
            $table->integer('score')->default(0); // Puntaje acumulado
            $table->smallInteger('correct_count')->default(0);
            $table->smallInteger('wrong_count')->default(0);
            $table->tinyInteger('status')->default(0); // 0=in_progress, 1=finished, 2=abandoned
            $table->boolean('reward_paid')->default(false);
            $table->string('locale', 5)->default('es');
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'created_at']);
            $table->index('quiz_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};