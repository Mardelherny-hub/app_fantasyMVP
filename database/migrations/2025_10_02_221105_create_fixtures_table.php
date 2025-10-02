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
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('gameweek_id')
                  ->constrained('gameweeks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('home_fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('away_fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->smallInteger('home_goals')->default(0); // Regla: diferencia 10pts = 1 gol
            $table->smallInteger('away_goals')->default(0);
            $table->tinyInteger('status')->default(0); // 0=pending, 1=finished
            
            // ========== CAMPOS DE PLAYOFFS ==========
            $table->boolean('is_playoff')->default(false);
            $table->tinyInteger('playoff_round')->nullable(); // 1=cuartos, 2=semis, 3=final
            $table->string('playoff_dependency')->nullable(); // 'winner_of_fixture_123' para semis/final
            
            $table->timestamps();

            // Un partido único por liga/gameweek/equipos
            $table->unique(['league_id', 'gameweek_id', 'home_fantasy_team_id', 'away_fantasy_team_id'], 'unique_fixture');
            
            // Índices
            $table->index(['league_id', 'gameweek_id']);
            $table->index(['league_id', 'is_playoff']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};