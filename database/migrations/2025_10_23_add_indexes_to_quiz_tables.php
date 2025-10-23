<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Añade índices para optimizar las queries más frecuentes
     * del módulo educativo - VERSIÓN CORREGIDA.
     */
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Verificar si los índices NO existen antes de crearlos
            if (!$this->indexExists('quiz_attempts', 'idx_attempts_user_date')) {
                $table->index(['user_id', 'created_at'], 'idx_attempts_user_date');
            }
            
            if (!$this->indexExists('quiz_attempts', 'idx_attempts_status_date')) {
                $table->index(['status', 'created_at'], 'idx_attempts_status_date');
            }
            
            // Solo crear si existe el campo total_points
            if (Schema::hasColumn('quiz_attempts', 'total_points') && 
                !$this->indexExists('quiz_attempts', 'idx_attempts_points_user')) {
                $table->index(['total_points', 'user_id'], 'idx_attempts_points_user');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            // Índice compuesto para selección de preguntas activas
            if (!$this->indexExists('questions', 'idx_questions_active')) {
                $table->index(['category_id', 'difficulty', 'is_active'], 'idx_questions_active');
            }
            
            // Índice para preguntas por categoría
            if (!$this->indexExists('questions', 'idx_questions_category')) {
                $table->index(['category_id', 'is_active'], 'idx_questions_category');
            }
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            // Índice para análisis de respuestas por attempt
            if (!$this->indexExists('quiz_attempt_answers', 'idx_answers_attempt_correct')) {
                $table->index(['quiz_attempt_id', 'is_correct'], 'idx_answers_attempt_correct');
            }
            
            // Índice para análisis de respuestas por pregunta
            if (!$this->indexExists('quiz_attempt_answers', 'idx_answers_question_correct')) {
                $table->index(['question_id', 'is_correct'], 'idx_answers_question_correct');
            }
        });

        // NO crear índice en quiz_categories porque no tiene is_active ni order
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'idx_attempts_user_date');
            $this->dropIndexIfExists($table, 'idx_attempts_status_date');
            $this->dropIndexIfExists($table, 'idx_attempts_points_user');
        });

        Schema::table('questions', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'idx_questions_active');
            $this->dropIndexIfExists($table, 'idx_questions_category');
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'idx_answers_attempt_correct');
            $this->dropIndexIfExists($table, 'idx_answers_question_correct');
        });
    }

    /**
     * Verifica si un índice existe en una tabla.
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $index]
        );
        
        return $result[0]->count > 0;
    }

    /**
     * Elimina un índice si existe.
     */
    private function dropIndexIfExists($table, string $index): void
    {
        if ($this->indexExists($table->getTable(), $index)) {
            $table->dropIndex($index);
        }
    }
};