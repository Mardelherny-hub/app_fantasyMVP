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
        Schema::create('player_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('market_value', 12, 2); // Valor de mercado del jugador
            $table->timestamp('updated_at');

            // Un valor único por jugador por temporada
            $table->unique(['player_id', 'season_id']);
            
            // Índices
            $table->index('player_id');
            $table->index('season_id');
            $table->index('market_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_valuations');
    }
};