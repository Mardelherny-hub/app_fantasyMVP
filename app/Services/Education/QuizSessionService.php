<?php

namespace App\Services\Education;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar sesiones de quiz (Quick Quiz).
 * 
 * Responsabilidades:
 * - Generar quiz aleatorio con preguntas
 * - Crear intento de quiz
 * - Shuffle de opciones en servidor
 * - Validar rate limiting
 */
class QuizSessionService
{
    /**
     * Genera un nuevo Quick Quiz para el usuario.
     * 
     * @param User $user
     * @param string $locale
     * @return array ['attempt' => QuizAttempt, 'questions' => array]
     * @throws \Exception
     */
    public function startQuickQuiz(User $user, string $locale = 'es'): array
    {
        // Validar rate limiting
        $this->validateRateLimit($user);

        // Obtener configuración
        $questionsCount = (int) Setting::get('quiz.quick_quiz.questions_count', 10);
        $timeLimit = (int) Setting::get('quiz.quick_quiz.time_limit_seconds', 300);
        $timePerQuestion = (int) Setting::get('quiz.quick_quiz.time_per_question', 30);
        $timeLimitSec = $questionsCount * $timePerQuestion;

        // Obtener o crear el Quiz base de tipo QUICK
        $quiz = Quiz::where('type', Quiz::TYPE_QUICK)
            ->where('locale', $locale)
            ->where('is_active', true)
            ->first();

        if (!$quiz) {
            throw new \Exception("No active Quick Quiz found for locale: {$locale}");
        }

        // Seleccionar preguntas aleatorias
        $questions = $this->selectRandomQuestions($questionsCount, $locale);

        if ($questions->count() < $questionsCount) {
            throw new \Exception("Not enough active questions available. Required: {$questionsCount}, Found: {$questions->count()}");
        }

        // Crear el intento
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => now(),
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'score' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'reward_paid' => false,
            'locale' => $locale,
        ]);

        // Preparar preguntas con opciones shuffled
        $questionsData = $this->prepareQuestionsWithShuffledOptions($questions, $locale);

        // Guardar en caché la sesión (para validación anti-cheat)
        $this->cacheQuizSession($attempt->id, $questionsData, $timeLimitSec);

        return [
            'attempt' => $attempt,
            'questions' => $questionsData,
            'time_limit_sec' => $timeLimitSec,
        ];
    }

    /**
     * Selecciona preguntas aleatorias activas.
     * 
     * @param int $count
     * @param string $locale
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function selectRandomQuestions(int $count, string $locale): \Illuminate\Database\Eloquent\Collection
    {
        return Question::with(['translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->with(['options.translations' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }

    /**
     * Prepara las preguntas con opciones en orden aleatorio.
     * 
     * @param \Illuminate\Database\Eloquent\Collection $questions
     * @param string $locale
     * @return array
     */
    protected function prepareQuestionsWithShuffledOptions($questions, string $locale): array
    {
        $prepared = [];

        foreach ($questions as $index => $question) {
            // Obtener opciones y mezclarlas
            $options = $question->options->shuffle()->map(function ($option) use ($locale) {
                return [
                    'id' => $option->id,
                    'text' => $option->getText($locale),
                    'order' => $option->order,
                ];
            })->values()->toArray();

            $prepared[] = [
                'id' => $question->id,
                'text' => $question->getText($locale),
                'difficulty' => $question->difficulty,
                'options' => $options,
                'order' => $index + 1,
            ];
        }

        return $prepared;
    }

    /**
     * Valida rate limiting (máximo de intentos por hora).
     * 
     * @param User $user
     * @return void
     * @throws \Exception
     */
    protected function validateRateLimit(User $user): void
    {
        $maxAttemptsPerHour = (int) Setting::get('quiz.rate_limit.max_per_hour', 10);
        $cacheKey = "quiz_attempts_count:user_{$user->id}";

        $attemptsCount = Cache::remember($cacheKey, 3600, function () use ($user) {
            return QuizAttempt::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subHour())
                ->count();
        });

        if ($attemptsCount >= $maxAttemptsPerHour) {
            throw new \Exception("Rate limit exceeded. Maximum {$maxAttemptsPerHour} attempts per hour.");
        }
    }

    /**
     * Guarda la sesión del quiz en caché para validación anti-cheat.
     * 
     * @param int $attemptId
     * @param array $questionsData
     * @param int $timeLimitSec
     * @return void
     */
    protected function cacheQuizSession(int $attemptId, array $questionsData, int $timeLimitSec): void
    {
        $sessionData = [
            'questions' => $questionsData,
            'started_at' => now()->timestamp,
            'time_limit_sec' => $timeLimitSec,
        ];

        // Guardar por el doble del tiempo límite (para permitir validación posterior)
        Cache::put("quiz_session:{$attemptId}", $sessionData, $timeLimitSec * 2);
    }

    /**
     * Obtiene la sesión del quiz desde caché.
     * 
     * @param int $attemptId
     * @return array|null
     */
    public function getQuizSession(int $attemptId): ?array
    {
        return Cache::get("quiz_session:{$attemptId}");
    }

    /**
     * Valida que una respuesta sea válida (pregunta pertenece al intento).
     * 
     * @param int $attemptId
     * @param int $questionId
     * @return bool
     */
    public function validateQuestionBelongsToAttempt(int $attemptId, int $questionId): bool
    {
        $session = $this->getQuizSession($attemptId);

        if (!$session) {
            return false;
        }

        $questionIds = collect($session['questions'])->pluck('id')->toArray();

        return in_array($questionId, $questionIds);
    }

    /**
     * Valida el tiempo tomado para responder (anti-cheat).
     * 
     * @param int $attemptId
     * @param int $timeTakenMs
     * @return bool
     */
    public function validateResponseTime(int $attemptId, int $timeTakenMs): bool
    {
        $session = $this->getQuizSession($attemptId);

        if (!$session) {
            return false;
        }

        $minTime = (int) Setting::get('quiz.min_answer_time_ms', 500);
        $maxTime = (int) Setting::get('quiz.max_answer_time_ms', 60000);
        return $timeTakenMs >= $minTime && $timeTakenMs <= $maxTime;
    }

    /**
     * Finaliza un intento de quiz.
     * 
     * @param QuizAttempt $attempt
     * @return void
     */
    public function finishAttempt(QuizAttempt $attempt): void
    {
        $attempt->update([
            'finished_at' => now(),
            'status' => QuizAttempt::STATUS_FINISHED,
        ]);

        // Limpiar caché de sesión
        Cache::forget("quiz_session:{$attempt->id}");

        // Limpiar caché de rate limiting para permitir actualización
        Cache::forget("quiz_attempts_count:user_{$attempt->user_id}");
    }

    /**
     * Abandona un intento de quiz.
     * 
     * @param QuizAttempt $attempt
     * @return void
     */
    public function abandonAttempt(QuizAttempt $attempt): void
    {
        $attempt->update([
            'finished_at' => now(),
            'status' => QuizAttempt::STATUS_ABANDONED,
        ]);

        // Limpiar caché
        Cache::forget("quiz_session:{$attempt->id}");
    }
}