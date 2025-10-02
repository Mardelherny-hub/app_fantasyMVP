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
        Schema::create('player_team_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('real_team_id')
                  ->constrained('real_teams')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // No borrar equipo si tiene historial
            $table->date('from_date');
            $table->date('to_date')->nullable(); // NULL = jugador actual del equipo
            $table->smallInteger('shirt_number')->nullable();
            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['player_id', 'real_team_id', 'from_date']);
            $table->index(['player_id', 'to_date']); // Para buscar equipo actual (to_date = null)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_team_history');
    }
};