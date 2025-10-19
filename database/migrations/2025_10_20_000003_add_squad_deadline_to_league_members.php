<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agrega campo squad_deadline_at a league_members para controlar
     * el deadline de 72 horas para completar el armado de plantilla.
     */
    public function up(): void
    {
        Schema::table('league_members', function (Blueprint $table) {
            $table->datetime('squad_deadline_at')
                  ->nullable()
                  ->after('joined_at')
                  ->comment('Deadline para completar armado de plantilla (joined_at + 72 horas)');
            
            // Índice para búsquedas de deadlines vencidos
            $table->index('squad_deadline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('league_members', function (Blueprint $table) {
            $table->dropIndex(['squad_deadline_at']);
            $table->dropColumn('squad_deadline_at');
        });
    }
};