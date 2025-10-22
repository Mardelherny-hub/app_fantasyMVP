<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrección: Cambiar FK de player_match_stats 
     * de football_matches (legacy) a real_matches (correcto)
     */
    public function up(): void
    {
        Schema::table('player_match_stats', function (Blueprint $table) {
            // 1. Eliminar FK antigua a football_matches
            $table->dropForeign(['match_id']);
            
            // 2. Renombrar columna match_id -> real_match_id
            $table->renameColumn('match_id', 'real_match_id');
        });
        
        // Separar en dos bloques para evitar conflictos con renameColumn
        Schema::table('player_match_stats', function (Blueprint $table) {
            // 3. Crear FK nueva a real_matches
            $table->foreign('real_match_id')
                  ->references('id')
                  ->on('real_matches')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_match_stats', function (Blueprint $table) {
            // 1. Eliminar FK nueva
            $table->dropForeign(['real_match_id']);
            
            // 2. Renombrar columna real_match_id -> match_id
            $table->renameColumn('real_match_id', 'match_id');
        });
        
        // Separar en dos bloques
        Schema::table('player_match_stats', function (Blueprint $table) {
            // 3. Restaurar FK antigua a football_matches
            $table->foreign('match_id')
                  ->references('id')
                  ->on('football_matches')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }
};