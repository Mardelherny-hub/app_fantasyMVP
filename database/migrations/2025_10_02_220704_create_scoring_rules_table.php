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
        Schema::create('scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('code'); // 'goal_gk', 'goal_df', 'goal_mf', 'goal_fw', 'assist', 'cs_gk', etc.
            $table->string('label'); // "Goal scored by Goalkeeper"
            $table->smallInteger('points'); // Puntos positivos o negativos
            $table->json('meta')->nullable(); // Configuraciones adicionales
            $table->timestamps();

            // Un código único por temporada
            $table->unique(['season_id', 'code']);
            
            // Índice
            $table->index('season_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
    }
};