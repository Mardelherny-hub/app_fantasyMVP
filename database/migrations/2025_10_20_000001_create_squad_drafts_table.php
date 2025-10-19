<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para almacenar borradores del armado de plantilla.
     * Permite al manager guardar su progreso y retomarlo después.
     * Se crea un borrador único por fantasy_team.
     */
    public function up(): void
    {
        Schema::create('squad_drafts', function (Blueprint $table) {
            $table->id();
            
            // Relación con el equipo fantasy
            $table->foreignId('fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            // Jugadores seleccionados (array de objetos)
            // Estructura: [{player_id, position, price, added_at}, ...]
            $table->json('selected_players')->nullable();
            
            // Control del wizard (paso actual: 1-5)
            // 1=GK, 2=DF, 3=MF, 4=FW, 5=Review
            $table->tinyInteger('current_step')->default(1);
            
            // Control de presupuesto
            $table->decimal('budget_spent', 12, 2)->default(0);
            $table->decimal('budget_remaining', 12, 2)->default(100.00);
            
            // Contadores por posición
            // Estructura: {1: count_gk, 2: count_df, 3: count_mf, 4: count_fw}
            $table->json('limits')->nullable();
            
            // Estado de completitud
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            
            $table->timestamps();
            
            // Un borrador único por equipo
            $table->unique('fantasy_team_id');
            
            // Índices para consultas frecuentes
            $table->index('is_completed');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squad_drafts');
    }
};