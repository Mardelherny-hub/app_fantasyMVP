<?php

namespace App\Jobs\Manager;

use App\Models\FantasyTeam;
use App\Services\Manager\AutoAssignmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoAssignSquadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The fantasy team to auto-assign.
     *
     * @var FantasyTeam
     */
    public $fantasyTeam;

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
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(FantasyTeam $fantasyTeam)
    {
        $this->fantasyTeam = $fantasyTeam;
    }

    /**
     * Execute the job.
     */
    public function handle(AutoAssignmentService $autoAssignmentService): void
    {
        try {
            Log::info('AutoAssignSquadJob: Starting auto-assignment', [
                'fantasy_team_id' => $this->fantasyTeam->id,
                'team_name' => $this->fantasyTeam->name,
                'is_bot' => $this->fantasyTeam->is_bot,
            ]);

            // Verificar que el equipo no tenga ya la plantilla completa
            if ($this->fantasyTeam->is_squad_complete) {
                Log::info('AutoAssignSquadJob: Squad already complete, skipping', [
                    'fantasy_team_id' => $this->fantasyTeam->id,
                ]);
                return;
            }

            // Ejecutar asignación automática
            $result = $autoAssignmentService->autoAssignSquad($this->fantasyTeam);

            Log::info('AutoAssignSquadJob: Auto-assignment completed successfully', [
                'fantasy_team_id' => $this->fantasyTeam->id,
                'players_assigned' => $result['players_count'] ?? 0,
                'budget_spent' => $result['total_spent'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('AutoAssignSquadJob: Failed to auto-assign squad', [
                'fantasy_team_id' => $this->fantasyTeam->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar la excepción para que el job se reintente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AutoAssignSquadJob: Job failed after all retries', [
            'fantasy_team_id' => $this->fantasyTeam->id,
            'team_name' => $this->fantasyTeam->name,
            'error' => $exception->getMessage(),
        ]);

        // Aquí podrías enviar una notificación al admin o al usuario
        // Notification::send($adminUsers, new SquadAutoAssignmentFailed($this->fantasyTeam));
    }
}