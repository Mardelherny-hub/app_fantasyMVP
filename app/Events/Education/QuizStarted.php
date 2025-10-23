<?php

namespace App\Events\Education;

use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un usuario inicia un quiz.
 */
class QuizStarted
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