<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Añade campos adicionales a quiz_attempt_answers para tracking
     * detallado y detección de patrones.
     */
    public function up(): void
    {
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            // Orden en que se mostró la opción (para detectar bias)
            $table->tinyInteger('option_order')->nullable()->after('selected_option_id')
                  ->comment('Posición de la opción seleccionada (1-4)');
            
            // Si el usuario cambió su respuesta
            $table->boolean('answer_changed')->default(false)->after('option_order')
                  ->comment('Si el usuario modificó su respuesta antes de confirmar');
            
            // Primera respuesta (si cambió)
            $table->foreignId('first_selected_option_id')->nullable()->after('answer_changed')
                  ->constrained('question_options')
                  ->onDelete('set null')
                  ->comment('Primera opción seleccionada si hubo cambio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempt_answers', function (Blueprint $table) {
            $table->dropForeign(['first_selected_option_id']);
            $table->dropColumn([
                'option_order',
                'answer_changed',
                'first_selected_option_id',
            ]);
        });
    }
};
