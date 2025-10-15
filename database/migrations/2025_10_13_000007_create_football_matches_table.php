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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->smallInteger('matchday'); // Jornada/fecha (1, 2, 3...)
            $table->foreignId('home_team_id')
                  ->constrained('real_teams')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->foreignId('away_team_id')
                  ->constrained('real_teams')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->datetime('starts_at');
            $table->tinyInteger('status')->default(0); // 0=pending, 1=live, 2=finished, 3=postponed
            $table->smallInteger('home_goals')->default(0);
            $table->smallInteger('away_goals')->default(0);
            $table->json('data')->nullable(); // Árbitro, estadio, etc.
            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['season_id', 'matchday']);
            $table->index('starts_at');
            $table->index('status');
            $table->index(['home_team_id', 'season_id']);
            $table->index(['away_team_id', 'season_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};