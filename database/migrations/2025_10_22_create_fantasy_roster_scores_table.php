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
        Schema::create('fantasy_roster_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fantasy_roster_id')
                  ->constrained('fantasy_rosters')
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
            $table->foreignId('fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->boolean('is_starter')->default(false);
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_vice_captain')->default(false);
            $table->integer('base_points')->default(0);
            $table->integer('final_points')->default(0);
            $table->json('breakdown')->nullable();
            $table->timestamps();

            // Índices únicos y compuestos
            $table->unique(['fantasy_roster_id', 'player_id', 'gameweek_id'], 'unique_roster_player_gw');
            $table->index(['fantasy_team_id', 'gameweek_id']);
            $table->index('gameweek_id');
            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fantasy_roster_scores');
    }
};
