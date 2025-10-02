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
        Schema::create('playoff_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->tinyInteger('phase'); // 1=cuartos, 2=semis, 3=final
            $table->string('match_label')->nullable(); // "4th vs 5th", "Winner Q1 vs 1st", etc.
            $table->foreignId('home_fantasy_team_id')
                  ->nullable()
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreignId('away_fantasy_team_id')
                  ->nullable()
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreignId('winner_fantasy_team_id')
                  ->nullable()
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreignId('fixture_id')
                  ->nullable()
                  ->constrained('fixtures')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->timestamps();

            // Índices
            $table->index(['league_id', 'season_id', 'phase']);
            $table->index('fixture_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playoff_brackets');
    }
};