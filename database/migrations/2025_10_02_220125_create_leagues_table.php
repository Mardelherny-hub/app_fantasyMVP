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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')
                  ->nullable() // AHORA ES OPCIONAL
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Si se borra el owner, queda NULL
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('name');
            $table->string('code')->unique(); // Código de invitación
            $table->tinyInteger('type')->default(1); // 1=privada, 2=pública
            $table->tinyInteger('max_participants')->default(10); // Cambio de 8 a 10
            $table->boolean('auto_fill_bots')->default(true);
            $table->boolean('is_locked')->default(false); // Cierra inscripciones
            $table->string('locale', 5)->default('es');
            
            // ========== CONFIGURACIÓN DE PLAYOFFS ==========
            $table->tinyInteger('playoff_teams')->default(5); // Top 5 clasifican
            $table->tinyInteger('playoff_format')->default(1); // 1=Page Playoff, 2=otros
            $table->smallInteger('regular_season_gameweeks')->default(27); // Fase regular
            $table->smallInteger('total_gameweeks')->default(30); // 27 regular + 3 playoffs
            
            $table->json('settings')->nullable(); // Reglas específicas adicionales
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('season_id');
            $table->index('owner_user_id');
            $table->index(['season_id', 'is_locked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};