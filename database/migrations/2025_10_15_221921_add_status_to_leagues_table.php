<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agregar campo status para gestionar aprobación de ligas privadas.
     */
    public function up(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            // Status de la liga
            // 0 = pending_approval (esperando aprobación admin)
            // 1 = approved (aprobada y activa)
            // 2 = rejected (rechazada por admin)
            // 3 = archived (finalizada/archivada)
            $table->tinyInteger('status')
                  ->default(0)
                  ->after('is_locked')
                  ->index();
                  
            // Notas de aprobación/rechazo del admin
            $table->text('admin_notes')->nullable()->after('status');
            
            // Fecha de aprobación/rechazo
            $table->timestamp('reviewed_at')->nullable()->after('admin_notes');
            
            // Usuario admin que revisó
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->after('reviewed_at')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'admin_notes', 'reviewed_at', 'reviewed_by']);
        });
    }
};