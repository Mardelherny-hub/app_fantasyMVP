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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('from_fantasy_team_id')
                  ->nullable() // NULL = jugador libre
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreignId('to_fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->tinyInteger('type'); // 1=buy, 2=loan_out, 3=loan_in, 4=release
            $table->datetime('effective_at');
            $table->json('meta')->nullable(); // Información adicional
            $table->timestamps();

            // Índices
            $table->index(['league_id', 'player_id', 'effective_at']);
            $table->index('from_fantasy_team_id');
            $table->index('to_fantasy_team_id');
            $table->index('effective_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};