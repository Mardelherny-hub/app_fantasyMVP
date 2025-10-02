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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('lender_fantasy_team_id') // Quien presta
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('borrower_fantasy_team_id') // Quien recibe
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('starts_gw_id')
                  ->constrained('gameweeks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('ends_gw_id')
                  ->constrained('gameweeks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('fee', 12, 2)->default(0); // Cuota del préstamo
            $table->tinyInteger('status')->default(0); // 0=ongoing, 1=finished, 2=canceled
            $table->timestamps();

            // Solo un préstamo activo por jugador por liga
            $table->unique(['player_id', 'league_id', 'status'], 'unique_active_loan');
            
            // Índices
            $table->index(['league_id', 'status']);
            $table->index('lender_fantasy_team_id');
            $table->index('borrower_fantasy_team_id');
            $table->index(['starts_gw_id', 'ends_gw_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};