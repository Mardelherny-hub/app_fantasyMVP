<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            // Agregar campos de fechas de inscripción
            $table->timestamp('registration_opens_at')->nullable()->after('locale');
            $table->timestamp('registration_closes_at')->nullable()->after('registration_opens_at');
            
            // Agregar campos de estado de aprobación (si no existen)
            if (!Schema::hasColumn('leagues', 'status')) {
                $table->tinyInteger('status')->default(0)->after('is_locked')
                    ->comment('0=pending, 1=approved, 2=rejected, 3=archived');
            }
            if (!Schema::hasColumn('leagues', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leagues', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('leagues', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')
                    ->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->dropColumn([
                'registration_opens_at',
                'registration_closes_at'
            ]);
        });
    }
};