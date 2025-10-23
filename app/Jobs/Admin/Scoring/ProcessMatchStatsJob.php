<?php

namespace App\Jobs\Admin\Scoring;

use App\Models\RealMatch;
use App\Models\PlayerMatchStats;
use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMatchStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public RealMatch $match
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing match stats", [
            'real_match_id' => $this->match->id,
            'match_status' => $this->match->status
        ]);

        if (!$this->match->isFinished()) {
            Log::warning("Match is not finished yet", [
                'real_match_id' => $this->match->id,
                'status' => $this->match->status
            ]);
            return;
        }

        DB::transaction(function () {
            $events = $this->match->events()->with('player')->get();
            $stats = $this->match->stats()->with('player')->get();

            // Agrupar eventos por jugador
            $playerEvents = $events->groupBy('real_player_id');
            
            // Agrupar stats por jugador
            $playerStats = $stats->keyBy('real_player_id');

            // Procesar cada jugador que participó
            $playersProcessed = 0;

            foreach ($playerEvents as $realPlayerId => $eventsForPlayer) {
                $stat = $playerStats->get($realPlayerId);
                $this->processPlayerStats($realPlayerId, $eventsForPlayer, $stat);
                $playersProcessed++;
            }

            // Procesar jugadores que tienen stats pero no eventos
            foreach ($playerStats as $realPlayerId => $stat) {
                if (!$playerEvents->has($realPlayerId)) {
                    $this->processPlayerStats($realPlayerId, collect(), $stat);
                    $playersProcessed++;
                }
            }

            Log::info("Match stats processed successfully", [
                'real_match_id' => $this->match->id,
                'players_processed' => $playersProcessed
            ]);
        });
    }

    /**
     * Procesar stats de un jugador específico.
     */
    private function processPlayerStats($realPlayerId, $events, $stat): void
    {
        // Buscar el Player (fantasy) correspondiente al RealPlayer
        $player = Player::where('real_player_id', $realPlayerId)->first();

        if (!$player) {
            Log::warning("Fantasy player not found for real player", [
                'real_player_id' => $realPlayerId
            ]);
            return;
        }

        // Calcular stats desde eventos
        $goals = $events->where('type', 'goal')->count();
        $assists = $events->where('type', 'assist')->count();
        $yellowCards = $events->where('type', 'yellow')->count();
        $redCards = $events->where('type', 'red')->count();
        $ownGoals = $events->where('type', 'own_goal')->count();
        $penaltyScored = $events->where('type', 'penalty_scored')->count();
        $penaltyMissed = $events->where('type', 'penalty_missed')->count();

        // Obtener minutos y otros stats del RealPlayerStat si existe
        $minutes = $stat?->minutes ?? 0;
        $saves = 0;
        $shotsOnTarget = 0;

        // Si hay rating en RealPlayerStat, usarlo
        $rating = $stat?->rating ?? null;

        // Determinar clean sheet (porteros y defensas)
        $cleanSheet = false;
        if (in_array($player->position, [Player::POSITION_GK, Player::POSITION_DF])) {
            // Clean sheet si jugó 60+ minutos y su equipo no recibió goles
            if ($minutes >= 60) {
                $playerTeamId = $events->first()?->real_team_id;
                if ($playerTeamId) {
                    $teamConceded = $this->getTeamGoalsConceded($playerTeamId);
                    $cleanSheet = $teamConceded === 0;
                }
            }
        }

        $goaltsConceded = $cleanSheet ? 0 : $this->getTeamGoalsConceded($events->first()?->real_team_id);

        // Crear o actualizar PlayerMatchStats
        PlayerMatchStats::updateOrCreate(
            [
                'real_match_id' => $this->match->id,
                'player_id' => $player->id
            ],
            [
                'minutes' => $minutes,
                'goals' => $goals,
                'assists' => $assists,
                'shots' => $shotsOnTarget,
                'saves' => $saves,
                'yellow' => $yellowCards,
                'red' => $redCards,
                'clean_sheet' => $cleanSheet,
                'conceded' => $goaltsConceded,
                'rating' => $rating,
                'raw' => [
                    'own_goals' => $ownGoals,
                    'penalty_scored' => $penaltyScored,
                    'penalty_missed' => $penaltyMissed,
                    'events_count' => $events->count()
                ]
            ]
        );

        Log::debug("Player stats processed", [
            'player_id' => $player->id,
            'minutes' => $minutes,
            'goals' => $goals,
            'assists' => $assists
        ]);
    }

    /**
     * Obtener goles recibidos por el equipo.
     */
    private function getTeamGoalsConceded($teamId): int
    {
        if (!$teamId) {
            return 0;
        }

        // Determinar si es equipo local o visitante
        if ($this->match->fixture->home_team_id === $teamId) {
            return $this->match->away_score ?? 0;
        } else {
            return $this->match->home_score ?? 0;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessMatchStatsJob failed permanently", [
            'real_match_id' => $this->match->id,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'stats',
            'match:' . $this->match->id
        ];
    }
}