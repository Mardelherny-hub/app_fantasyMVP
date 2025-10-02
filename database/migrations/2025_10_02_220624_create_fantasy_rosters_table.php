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
        Schema::create('fantasy_rosters', function (Blueprint $table) {
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
            $table->tinyInteger('slot'); // Posición en el plantel (1-11 titulares, 12-18 suplentes)
            $table->boolean('is_starter')->default(true);
            $table->tinyInteger('captaincy')->default(0); // 0=none, 1=captain, 2=vice-captain
            $table->timestamps();

            // Un jugador solo puede estar una vez por equipo por gameweek
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
        Schema::dropIfExists('fantasy_rosters');
    }
};