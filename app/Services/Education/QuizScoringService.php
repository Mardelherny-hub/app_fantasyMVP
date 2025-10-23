<?php

namespace App\Services\Education;

use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\Question;

/**
 * Servicio de cálculo de puntuación para el módulo educativo.
 * 
 * VERSIÓN CORREGIDA - Usa los campos reales de la BD:
 * - score (no total_points)
 * - correct_count (no total_correct)
 * - wrong_count (no total_incorrect)
 * - finished_at (no completed_at)
 */
class QuizScoringService
{
    /**
     * Calcula la puntuación total de un intento de quiz.
     */
    public function calculateScore(QuizAttempt $attempt): array
    {
        $answers = $attempt->quizAttemptAnswers()
            ->with('question')
            ->orderBy('answered_at')
            ->get();

        $totalPoints = 0;
        $totalCorrect = 0;
        $totalWrong = 0;
        $streak = 0;
        $breakdown = [];

        foreach ($answers as $answer) {
            $questionPoints = $this->calculateQuestionPoints($answer, $streak);

            $totalPoints += $questionPoints['total'];
            
            if ($answer->is_correct) {
                $totalCorrect++;
                $streak++;
            } else {
                $totalWrong++;
                $streak = 0;
            }

            $breakdown[] = [
                'question_id' => $answer->question_id,
                'is_correct' => $answer->is_correct,
                'base_points' => $questionPoints['base'],
                'speed_bonus' => $questionPoints['speed_bonus'],
                'streak_bonus' => $questionPoints['streak_bonus'],
                'total_points' => $questionPoints['total'],
                'time_taken_ms' => $answer->time_taken_ms,
                'streak' => $streak,
            ];
        }

        return [
            'total_points' => $totalPoints,
            'total_correct' => $totalCorrect,
            'total_wrong' => $totalWrong,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calcula los puntos de una pregunta individual.
     */
    public function calculateQuestionPoints(QuizAttemptAnswer $answer, int $currentStreak): array
    {
        if (!$answer->is_correct) {
            return [
                'base' => 0,
                'speed_bonus' => 0,
                'streak_bonus' => 0,
                'total' => 0,
            ];
        }

        $basePoints = $this->getBasePoints($answer->question->difficulty);
        $speedBonus = $this->calculateSpeedBonus($answer->time_taken_ms);
        $streakBonus = $this->calculateStreakBonus($currentStreak);

        $answer->update([
            'points_awarded' => $basePoints + $speedBonus + $streakBonus,
        ]);

        return [
            'base' => $basePoints,
            'speed_bonus' => $speedBonus,
            'streak_bonus' => $streakBonus,
            'total' => $basePoints + $speedBonus + $streakBonus,
        ];
    }

    protected function getBasePoints(int $difficulty): int
    {
        return match ($difficulty) {
            Question::DIFFICULTY_EASY => (int) settings('quiz.points.easy', 10),
            Question::DIFFICULTY_MEDIUM => (int) settings('quiz.points.medium', 20),
            Question::DIFFICULTY_HARD => (int) settings('quiz.points.hard', 30),
            default => 10,
        };
    }

    protected function calculateSpeedBonus(?int $timeMs): int
    {
        if (!$timeMs) {
            return 0;
        }

        $speedThreshold = (int) settings('quiz.points.speed_threshold', 10) * 1000;
        $speedBonus = (int) settings('quiz.points.speed_bonus', 5);

        return ($timeMs < $speedThreshold) ? $speedBonus : 0;
    }

    protected function calculateStreakBonus(int $currentStreak): int
    {
        $streakThreshold = (int) settings('quiz.points.streak_threshold', 5);
        $streakBonus = (int) settings('quiz.points.streak_bonus', 10);

        $nextStreak = $currentStreak + 1;
        
        if ($nextStreak < $streakThreshold) {
            return 0;
        }

        $multiplier = floor($nextStreak / $streakThreshold) - floor($currentStreak / $streakThreshold);
        
        return $streakBonus * $multiplier;
    }

    /**
     * Actualiza el attempt con los puntos calculados.
     * USA LOS CAMPOS REALES: score, correct_count, wrong_count
     */
    public function updateAttemptScore(QuizAttempt $attempt, array $scoreData): QuizAttempt
    {
        $attempt->update([
            'score' => $scoreData['total_points'],              // ← CORREGIDO
            'correct_count' => $scoreData['total_correct'],     // ← CORREGIDO
            'wrong_count' => $scoreData['total_wrong'],         // ← CORREGIDO
        ]);

        return $attempt->fresh();
    }

    /**
     * Calcula la duración del intento en segundos.
     * USA EL CAMPO REAL: finished_at
     */
    public function calculateDuration(QuizAttempt $attempt): ?int
    {
        if (!$attempt->started_at || !$attempt->finished_at) {  // ← CORREGIDO
            return null;
        }

        return $attempt->started_at->diffInSeconds($attempt->finished_at);  // ← CORREGIDO
    }

    /**
     * Obtiene estadísticas de rendimiento del intento.
     */
    public function getAttemptStats(QuizAttempt $attempt): array
    {
        $answers = $attempt->quizAttemptAnswers;
        $totalQuestions = $answers->count();
        $correctAnswers = $answers->where('is_correct', true)->count();

        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $totalQuestions - $correctAnswers,
            'accuracy_percentage' => $totalQuestions > 0 
                ? round(($correctAnswers / $totalQuestions) * 100, 2) 
                : 0,
            'total_points' => $attempt->score ?? 0,              // ← CORREGIDO
            'duration_seconds' => $attempt->duration_seconds,
            'average_time_per_question' => $totalQuestions > 0 && $attempt->duration_seconds
                ? round($attempt->duration_seconds / $totalQuestions, 2)
                : null,
        ];
    }
}