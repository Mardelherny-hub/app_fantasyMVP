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
        Schema::create('fantasy_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('gameweek_id')
                  ->constrained('gameweeks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->smallInteger('base_points')->default(0);
            $table->smallInteger('bonus_points')->default(0); // Capitán, bonos especiales
            $table->smallInteger('total_points')->default(0)->index();
            $table->json('breakdown')->nullable(); // Desglose detallado de cómo se calculó
            $table->timestamps();

            // Un jugador solo puede tener puntos una vez por equipo por gameweek
            $table->unique(['fantasy_team_id', 'player_id', 'gameweek_id']);
            
            // Índices para consultas frecuentes
            $table->index(['fantasy_team_id', 'gameweek_id']);
            $table->index(['player_id', 'gameweek_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fantasy_points');
    }
};