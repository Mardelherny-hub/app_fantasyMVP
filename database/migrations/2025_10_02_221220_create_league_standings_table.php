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
        Schema::create('league_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('gameweek_id')
                  ->nullable() // NULL = standing actual/acumulado
                  ->constrained('gameweeks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->tinyInteger('position')->index(); // Posición en la tabla
            $table->smallInteger('played')->default(0); // Partidos jugados
            $table->smallInteger('won')->default(0);
            $table->smallInteger('drawn')->default(0);
            $table->smallInteger('lost')->default(0);
            $table->integer('goals_for')->default(0); // Goles a favor
            $table->integer('goals_against')->default(0); // Goles en contra
            $table->integer('goal_difference')->default(0); // Diferencia de goles
            $table->integer('points')->default(0)->index(); // Puntos de liga (3=win, 1=draw)
            $table->integer('fantasy_points')->default(0); // Puntos fantasy acumulados
            $table->timestamps();

            // Una posición única por liga/equipo/gameweek
            $table->unique(['league_id', 'fantasy_team_id', 'gameweek_id']);
            
            // Índices
            $table->index(['league_id', 'gameweek_id', 'position']);
            $table->index(['league_id', 'points', 'goal_difference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_standings');
    }
};