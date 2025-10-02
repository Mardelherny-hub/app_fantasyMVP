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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Si se borra el user, mantener log
            
            $table->string('action'); // 'player_imported', 'score_updated', 'league_created'
            
            // Morph para relacionar con cualquier modelo
            $table->string('auditable_type'); // 'App\Models\Player'
            $table->unsignedBigInteger('auditable_id');
            
            $table->json('old_values')->nullable(); // Estado anterior
            $table->json('new_values')->nullable(); // Estado nuevo
            
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6
            $table->text('user_agent')->nullable();
            
            $table->timestamp('created_at'); // Solo created_at, no updated_at
            
            // Índices para búsquedas eficientes
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};