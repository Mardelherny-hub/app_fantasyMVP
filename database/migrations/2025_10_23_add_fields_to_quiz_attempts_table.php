<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Añade campos adicionales a quiz_attempts de forma SEGURA.
     * Verifica si los campos existen antes de crearlos.
     */
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Solo añadir si NO existe
            if (!Schema::hasColumn('quiz_attempts', 'total_correct')) {
                $table->integer('total_correct')->default(0)->after('wrong_count')
                      ->comment('Cantidad de respuestas correctas');
            }
            
            if (!Schema::hasColumn('quiz_attempts', 'total_points')) {
                $table->integer('total_points')->default(0)->after('total_correct')
                      ->comment('Puntos totales obtenidos');
            }
            
            if (!Schema::hasColumn('quiz_attempts', 'duration_seconds')) {
                $table->integer('duration_seconds')->nullable()->after('finished_at')
                      ->comment('Duración total del intento en segundos');
            }
            
            if (!Schema::hasColumn('quiz_attempts', 'questions_snapshot')) {
                $table->json('questions_snapshot')->nullable()->after('duration_seconds')
                      ->comment('IDs de las preguntas en el orden presentado');
            }
            
            if (!Schema::hasColumn('quiz_attempts', 'metadata')) {
                $table->json('metadata')->nullable()->after('questions_snapshot')
                      ->comment('Información adicional (IP, user agent, etc)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $columns = [
                'total_correct',
                'total_points',
                'duration_seconds',
                'questions_snapshot',
                'metadata',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('quiz_attempts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};