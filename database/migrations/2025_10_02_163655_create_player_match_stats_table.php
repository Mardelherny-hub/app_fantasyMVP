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
        Schema::create('player_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')
                  ->constrained('football_matches')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->smallInteger('minutes')->default(0);
            $table->smallInteger('goals')->default(0);
            $table->smallInteger('assists')->default(0);
            $table->smallInteger('shots')->default(0);
            $table->smallInteger('saves')->default(0);
            $table->smallInteger('yellow')->default(0); // Tarjetas amarillas
            $table->smallInteger('red')->default(0); // Tarjetas rojas
            $table->boolean('clean_sheet')->default(false); // Portería a cero
            $table->smallInteger('conceded')->default(0); // Goles recibidos
            $table->decimal('rating', 4, 2)->nullable(); // Rating del jugador (0-10)
            $table->json('raw')->nullable(); // Estadísticas adicionales en formato JSON
            $table->timestamps();

            // Un jugador solo puede tener un registro por partido
            $table->unique(['match_id', 'player_id']);
            
            // Índices para consultas frecuentes
            $table->index('player_id');
            $table->index('match_id');
            $table->index(['player_id', 'match_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};