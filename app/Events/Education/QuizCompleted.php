<?php

namespace App\Events\Education;

use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un usuario completa un quiz.
 * 
 * Este evento desencadena:
 * - Cálculo de puntuación
 * - Otorgamiento de recompensas
 * - Actualización de estadísticas
 */
class QuizCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public QuizAttempt $attempt;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, QuizAttempt $attempt)
    {
        $this->user = $user;
        $this->attempt = $attempt;
    }
}