<?php

namespace App\Livewire\Dashboard\Education;

use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Services\Education\QuizSessionService;
use App\Services\Education\QuizScoringService;
use App\Services\Education\QuizRewardsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Componente Livewire para el Quick Quiz.
 * 
 * Responsabilidades:
 * - Iniciar quiz
 * - Mostrar preguntas con cronómetro
 * - Validar respuestas
 * - Finalizar quiz y mostrar resultados
 */
class QuickQuiz extends Component
{
    // Estado del quiz
    public $attemptId;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $timeLimitSec = 30;
    public $timeRemaining = 30;
    public $quizStarted = false;
    public $quizFinished = false;

    // Respuestas del usuario
    public $selectedOptionId = null;
    public $answers = [];

    // Estadísticas
    public $correctCount = 0;
    public $wrongCount = 0;
    public $totalScore = 0;

    // Mensajes
    public $errorMessage = null;

    protected QuizSessionService $sessionService;
    protected QuizScoringService $scoringService;
    protected QuizRewardsService $rewardsService;

    public function boot(
        QuizSessionService $sessionService,
        QuizScoringService $scoringService,
        QuizRewardsService $rewardsService
    ) {
        $this->sessionService = $sessionService;
        $this->scoringService = $scoringService;
        $this->rewardsService = $rewardsService;
    }

    /**
     * Inicia un nuevo Quick Quiz.
     */
    public function startQuiz()
    {
        try {
            $user = Auth::user();
            $locale = app()->getLocale();

            // Generar quiz
            $quizData = $this->sessionService->startQuickQuiz($user, $locale);

            $this->attemptId = $quizData['attempt']->id;
            $this->questions = $quizData['questions'];
            $this->timeLimitSec = $quizData['time_limit_sec'] / count($this->questions);
            $this->timeRemaining = $this->timeLimitSec;
            $this->currentQuestionIndex = 0;
            $this->quizStarted = true;
            $this->errorMessage = null;

            // Inicializar array de respuestas
            $this->answers = array_fill(0, count($this->questions), null);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Selecciona una opción de respuesta.
     */
    public function selectOption($optionId)
    {
        $this->selectedOptionId = $optionId;
    }

    /**
     * Envía la respuesta actual y avanza a la siguiente pregunta.
     */
    public function submitAnswer()
    {
         if ($this->selectedOptionId === null) {
            return;
        }

        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        
        // ✅ VALIDAR QUE NO SE HAYA RESPONDIDO YA ESTA PREGUNTA
        if (isset($this->answers[$this->currentQuestionIndex])) {
            // Ya fue respondida, no hacer nada
            return;
        }

        // ✅ VALIDAR QUE NO EXISTA EN LA BD
        $alreadyAnswered = QuizAttemptAnswer::where('quiz_attempt_id', $this->attemptId)
            ->where('question_id', $currentQuestion['id'])
            ->exists();
        
        if ($alreadyAnswered) {
            // Ya existe en BD, avanzar a la siguiente pregunta
            $this->selectedOptionId = null;
            if ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
                $this->timeRemaining = $this->timeLimitSec;
            }
            return;
        }

        $user = Auth::user();
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        
        // Calcular tiempo tomado (en ms)
        $timeTakenMs = ($this->timeLimitSec - $this->timeRemaining) * 1000;

        // Validar tiempo (anti-cheat)
        if (!$this->sessionService->validateResponseTime($this->attemptId, $timeTakenMs)) {
            $timeTakenMs = $this->timeLimitSec * 1000; // Usar tiempo máximo si es inválido
        }

        try {
            // Verificar si la respuesta es correcta
            $correctOption = collect($currentQuestion['options'])
                ->first(function ($option) use ($currentQuestion) {
                    // La opción correcta se determina en el backend
                    $question = \App\Models\Question::find($currentQuestion['id']);
                    return $option['id'] === $question->getCorrectOption()->id;
                });

            $isCorrect = ($this->selectedOptionId === $correctOption['id']);

            // Calcular racha actual
            $currentStreak = $this->calculateCurrentStreak();

            // Crear el registro de respuesta primero (sin puntos)
            $answerRecord = QuizAttemptAnswer::create([
                'quiz_attempt_id' => $this->attemptId,
                'question_id' => $currentQuestion['id'],
                'selected_option_id' => $this->selectedOptionId,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
                'time_taken_ms' => $timeTakenMs,
                'points_awarded' => 0, // Se actualizará después
            ]);

            // Obtener la pregunta completa con relación
            $answerRecord->load('question');

            // Calcular puntos usando el método correcto
            $pointsData = $this->scoringService->calculateQuestionPoints(
                $answerRecord,
                $currentStreak
            );

            $points = $pointsData['total'];

            // Guardar respuesta en BD
            QuizAttemptAnswer::create([
                'quiz_attempt_id' => $this->attemptId,
                'question_id' => $currentQuestion['id'],
                'selected_option_id' => $this->selectedOptionId,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
                'time_taken_ms' => $timeTakenMs,
                'points_awarded' => $points,
            ]);

            // Actualizar contadores
            if ($isCorrect) {
                $this->correctCount++;
            } else {
                $this->wrongCount++;
            }

            $this->totalScore += $points;

            // Guardar respuesta en el array local
            $this->answers[$this->currentQuestionIndex] = [
                'question_id' => $currentQuestion['id'],
                'selected_option_id' => $this->selectedOptionId,
                'is_correct' => $isCorrect,
                'points' => $points,
            ];

            // Resetear selección
            $this->selectedOptionId = null;

            // Avanzar a la siguiente pregunta o finalizar
            if ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
                $this->timeRemaining = $this->timeLimitSec;
            } else {
                $this->finishQuiz();
            }

        } catch (\Exception $e) {
            $this->errorMessage = 'Error al procesar la respuesta: ' . $e->getMessage();
        }
    }

    /**
     * Salta la pregunta actual (no responde).
     */
    public function skipQuestion()
    {
        $currentQuestion = $this->questions[$this->currentQuestionIndex];

        // Guardar respuesta vacía en BD
        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $this->attemptId,
            'question_id' => $currentQuestion['id'],
            'selected_option_id' => null,
            'is_correct' => false,
            'answered_at' => now(),
            'time_taken_ms' => $this->timeLimitSec * 1000,
            'points_awarded' => 0,
        ]);

        $this->wrongCount++;

        // Guardar respuesta en el array local
        $this->answers[$this->currentQuestionIndex] = [
            'question_id' => $currentQuestion['id'],
            'selected_option_id' => null,
            'is_correct' => false,
            'points' => 0,
        ];

        // Avanzar o finalizar
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->timeRemaining = $this->timeLimitSec;
            $this->selectedOptionId = null;
        } else {
            $this->finishQuiz();
        }
    }

    /**
     * Finaliza el quiz y procesa recompensas.
     */
    public function finishQuiz()
    {
        try {
            $user = Auth::user();
            $attempt = QuizAttempt::find($this->attemptId);

            DB::transaction(function () use ($attempt, $user) {
                // Actualizar score del attempt
                $this->scoringService->updateAttemptScore($attempt);

                // Finalizar attempt
                $this->sessionService->finishAttempt($attempt);

                // Otorgar recompensas
                $this->rewardsService->awardRewards($user, $attempt);
            });

            // Marcar como finalizado
            $this->quizFinished = true;

            // Redirigir a resultados después de 2 segundos
            $this->dispatch('quiz-finished', attemptId: $this->attemptId);

        } catch (\Exception $e) {
            $this->errorMessage = 'Error al finalizar el quiz: ' . $e->getMessage();
        }
    }

    /**
     * Calcula la racha actual de respuestas correctas.
     */
    protected function calculateCurrentStreak(): int
    {
        $streak = 0;

        for ($i = $this->currentQuestionIndex - 1; $i >= 0; $i--) {
            if ($this->answers[$i] && $this->answers[$i]['is_correct']) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Actualiza el cronómetro (llamado desde JavaScript).
     */
    public function updateTimer($timeRemaining)
    {
        $this->timeRemaining = $timeRemaining;

        // Si se acabó el tiempo, saltar la pregunta automáticamente
        if ($this->timeRemaining <= 0) {
            $this->skipQuestion();
        }
    }

    /**
     * Reinicia el quiz para jugar de nuevo.
     */
    public function restartQuiz()
    {
        $this->reset([
            'attemptId',
            'questions',
            'currentQuestionIndex',
            'timeRemaining',
            'quizStarted',
            'quizFinished',
            'selectedOptionId',
            'answers',
            'correctCount',
            'wrongCount',
            'totalScore',
            'errorMessage'
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.education.quick-quiz');
    }
}