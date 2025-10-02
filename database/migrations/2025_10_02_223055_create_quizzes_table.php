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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->nullable() // NULL = aleatorio/mixto
                  ->constrained('quiz_categories')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->tinyInteger('type'); // 1=quick, 2=thematic, 3=pvp
            $table->string('title');
            $table->string('locale', 5)->default('es');
            $table->smallInteger('questions_count')->default(10); // Cantidad de preguntas
            $table->smallInteger('time_limit_sec')->default(30); // Tiempo por pregunta en segundos
            $table->decimal('reward_amount', 14, 2)->default(0); // Recompensa en monedas
            $table->json('settings')->nullable(); // Configuraciones adicionales
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('category_id');
            $table->index('type');
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};