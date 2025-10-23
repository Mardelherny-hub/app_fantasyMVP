<?php

namespace App\Events\Education;

use App\Models\User;
use App\Models\Question;
use App\Models\QuizAttemptAnswer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un usuario responde una pregunta.
 * 
 * Útil para tracking en tiempo real y analíticas.
 */
class QuestionAnswered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Question $question;
    public QuizAttemptAnswer $answer;
    public bool $isCorrect;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        Question $question,
        QuizAttemptAnswer $answer,
        bool $isCorrect
    ) {
        $this->user = $user;
        $this->question = $question;
        $this->answer = $answer;
        $this->isCorrect = $isCorrect;
    }
}