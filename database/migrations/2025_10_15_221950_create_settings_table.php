<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de configuraciones globales del sistema.
     * Permite almacenar cualquier configuración de forma dinámica.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Clave única del setting (ej: 'max_leagues_per_user')
            $table->string('key', 100)->unique();
            
            // Valor del setting (JSON para flexibilidad)
            $table->text('value')->nullable();
            
            // Grupo/Categoría del setting (ej: 'general', 'leagues', 'market', 'quiz')
            $table->string('group', 50)->default('general')->index();
            
            // Tipo de dato para validación (string, integer, boolean, json, array)
            $table->string('type', 20)->default('string');
            
            // Label amigable para UI admin
            $table->string('label')->nullable();
            
            // Descripción/ayuda del setting
            $table->text('description')->nullable();
            
            // Opciones para select/radio (JSON)
            $table->json('options')->nullable();
            
            // Valor por defecto
            $table->text('default_value')->nullable();
            
            // Reglas de validación Laravel
            $table->string('validation_rules')->nullable();
            
            // Si es editable desde UI o solo desde código/seed
            $table->boolean('is_editable')->default(true);
            
            // Si está activo o deshabilitado temporalmente
            $table->boolean('is_active')->default(true);
            
            // Orden para mostrar en UI
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Índices compuestos
            $table->index(['group', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};