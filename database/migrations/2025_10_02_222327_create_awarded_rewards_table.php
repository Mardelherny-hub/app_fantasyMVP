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
        Schema::create('awarded_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')
                  ->constrained('rewards')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('source_type')->nullable(); // Morph: 'quiz_attempts', 'fixtures'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->datetime('paid_at');
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'paid_at']);
            $table->index(['source_type', 'source_id']);
            $table->index('reward_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awarded_rewards');
    }
};