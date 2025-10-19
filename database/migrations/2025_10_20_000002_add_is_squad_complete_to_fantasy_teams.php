<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agrega campo is_squad_complete a fantasy_teams para controlar
     * si el manager ya completó el armado de su plantilla inicial.
     */
    public function up(): void
    {
        Schema::table('fantasy_teams', function (Blueprint $table) {
            $table->boolean('is_squad_complete')
                  ->default(false)
                  ->after('is_bot')
                  ->comment('Indica si el manager completó el armado de su plantilla inicial');
            
            // Índice para búsquedas frecuentes
            $table->index('is_squad_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fantasy_teams', function (Blueprint $table) {
            $table->dropIndex(['is_squad_complete']);
            $table->dropColumn('is_squad_complete');
        });
    }
};