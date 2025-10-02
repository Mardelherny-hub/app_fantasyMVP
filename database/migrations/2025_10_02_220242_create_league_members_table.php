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
        Schema::create('league_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->tinyInteger('role')->default(1); // 1=participant, 2=manager, 3=moderator
            $table->datetime('joined_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Un usuario solo puede estar una vez en una liga
            $table->unique(['league_id', 'user_id']);
            
            // Índices
            $table->index('league_id');
            $table->index('user_id');
            $table->index(['league_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_members');
    }
};