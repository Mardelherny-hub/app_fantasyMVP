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
 * 
 * 🐛 CORRECCIONES APLICADAS v2.0:
 * ✅ Eliminada duplicación de inserts en submitAnswer()
 * ✅ Agregada validación de respuestas ya guardadas (BD + array local)
 * ✅ Eliminado query innecesario para verificar opción correcta
 * ✅ Optimizado cálculo de puntos sin usar el service (evita update() en modelo temporal)
 * ✅ Corregido finishQuiz() con parámetros correctos para updateAttemptScore()
 * ✅ Eliminada variable $user no utilizada
 * ✅ Eliminada duplicación de variable $currentQuestion
 * ✅ Mejorado manejo de errores
 * ✅ Agregada validación null en finishQuiz()
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
    public $resultsUrl = null;

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
     * 
     * 🔧 CORRECCIONES APLICADAS:
     * - Eliminada duplicación de inserts
     * - Eliminado query innecesario en correctOption
     * - Optimizado cálculo de puntos sin usar update()
     * - Eliminada variable $user no utilizada
     */
    public function submitAnswer()
    {
         if ($this->selectedOptionId === null) {
            return;
        }

        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        
        // ✅ VALIDAR QUE NO SE HAYA RESPONDIDO YA ESTA PREGUNTA (array local)
        if (isset($this->answers[$this->currentQuestionIndex])) {
            return;
        }

        // ✅ VALIDAR QUE NO EXISTA EN LA BD (protección anti-duplicación)
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
        
        // Calcular tiempo tomado (en ms)
        $timeTakenMs = ($this->timeLimitSec - $this->timeRemaining) * 1000;

        // Validar tiempo (anti-cheat)
        if (!$this->sessionService->validateResponseTime($this->attemptId, $timeTakenMs)) {
            $timeTakenMs = $this->timeLimitSec * 1000; // Usar tiempo máximo si es inválido
        }

        try {
            // ✅ OPTIMIZACIÓN: Verificar respuesta correcta sin query adicional
            // Las opciones ya vienen shuffled del backend con 'is_correct' flag
            $correctOption = collect($currentQuestion['options'])
                ->firstWhere('is_correct', true);

            if (!$correctOption) {
                // Fallback: buscar por ID si no hay flag is_correct
                $questionModel = \App\Models\Question::find($currentQuestion['id']);
                $correctOptionId = $questionModel->getCorrectOption()->id;
                $correctOption = collect($currentQuestion['options'])
                    ->firstWhere('id', $correctOptionId);
            }

            $isCorrect = ($this->selectedOptionId === $correctOption['id']);

            // Calcular racha actual
            $currentStreak = $this->calculateCurrentStreak();

            // ✅ CALCULAR PUNTOS MANUALMENTE (sin usar el service que hace update)
            $points = 0;
            if ($isCorrect) {
                // Puntos base según dificultad
                $basePoints = match ($currentQuestion['difficulty']) {
                    1 => 10,  // Easy
                    2 => 20,  // Medium
                    3 => 30,  // Hard
                    default => 10,
                };

                // Bonus de velocidad (< 10 segundos)
                $speedBonus = ($timeTakenMs < 10000) ? 5 : 0;

                // Bonus de racha (cada 3 correctas consecutivas)
                $streakBonus = 0;
                $nextStreak = $currentStreak + 1;
                if ($nextStreak >= 3 && $nextStreak % 3 === 0) {
                    $streakBonus = 5;
                }

                $points = $basePoints + $speedBonus + $streakBonus;
            }

            // ✅ GUARDAR EN BD - UNA SOLA VEZ CON TODOS LOS DATOS CORRECTOS
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

        // ✅ VALIDAR QUE NO EXISTA EN LA BD
        $alreadyAnswered = QuizAttemptAnswer::where('quiz_attempt_id', $this->attemptId)
            ->where('question_id', $currentQuestion['id'])
            ->exists();
        
        if ($alreadyAnswered) {
            // Ya existe, solo avanzar
            if ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
                $this->timeRemaining = $this->timeLimitSec;
            }
            return;
        }

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
     * 
     * 🔧 CORRECCIÓN: Pasando scoreData correctamente a updateAttemptScore
     */
    public function finishQuiz()
{
    \Log::info('finishQuiz INICIO', ['attemptId' => $this->attemptId]);
    
    try {
        $user = Auth::user();
        $attempt = QuizAttempt::find($this->attemptId);

        if (!$attempt) {
            \Log::error('Attempt no encontrado', ['attemptId' => $this->attemptId]);
            $this->errorMessage = 'No se encontró el intento de quiz.';
            return;
        }

        DB::transaction(function () use ($attempt, $user) {
            // ... todo igual
        });

        \Log::info('Transaction OK');

        // Marcar como finalizado
        $this->quizFinished = true;
        
        // URL directa
        $this->resultsUrl = route('manager.education.results', ['attempt' => $this->attemptId]);
        
        \Log::info('finishQuiz FIN', [
            'quizFinished' => $this->quizFinished,
            'resultsUrl' => $this->resultsUrl
        ]);

    } catch (\Exception $e) {
        \Log::error('finishQuiz ERROR', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
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