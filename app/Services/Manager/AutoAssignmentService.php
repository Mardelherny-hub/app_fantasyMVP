<?php

namespace App\Services\Manager;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\SquadDraft;
use App\Models\PlayerValuation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAssignmentService
{
    /**
     * Asignar plantilla automáticamente cuando vence el deadline
     * 
     * Estrategia: Seleccionar jugadores baratos pero válidos respetando:
     * - Presupuesto de $100
     * - Límites por posición
     * - Total de 23 jugadores
     */
    public function autoAssignSquad(FantasyTeam $team): array
    {
        return DB::transaction(function () use ($team) {
            $seasonId = $team->league->season_id;
            $budget = SquadBuilderService::INITIAL_BUDGET;
            $selectedPlayers = [];
            
            Log::info("Auto-asignando plantilla para equipo: {$team->id}");
            
            // Distribución estratégica de jugadores
            // Total: 23 = 2 GK + 6 DF + 8 MF + 7 FW
            $positionsToSelect = [
                Player::POSITION_GK => 2,  // 2 arqueros
                Player::POSITION_DF => 6,  // 6 defensores
                Player::POSITION_MF => 8,  // 8 mediocampistas
                Player::POSITION_FW => 7,  // 7 delanteros
            ];
            
            // Seleccionar jugadores por posición
            foreach ($positionsToSelect as $position => $count) {
                $selected = $this->selectPlayersByPosition($position, $count, $seasonId, $budget);
                
                foreach ($selected as $playerData) {
                    $selectedPlayers[] = $playerData;
                    $budget -= $playerData['price'];
                }
            }
            
            // Validar que se seleccionaron 23 jugadores
            if (count($selectedPlayers) !== SquadBuilderService::SQUAD_SIZE) {
                Log::warning("Auto-asignación incompleta. Jugadores seleccionados: " . count($selectedPlayers));
            }
            
            // Crear o actualizar borrador
            $draft = SquadDraft::updateOrCreate(
                ['fantasy_team_id' => $team->id],
                [
                    'selected_players' => $selectedPlayers,
                    'budget_spent' => SquadBuilderService::INITIAL_BUDGET - $budget,
                    'budget_remaining' => $budget,
                    'limits' => [
                        1 => $positionsToSelect[Player::POSITION_GK],
                        2 => $positionsToSelect[Player::POSITION_DF],
                        3 => $positionsToSelect[Player::POSITION_MF],
                        4 => $positionsToSelect[Player::POSITION_FW],
                    ],
                    'is_completed' => true,
                    'completed_at' => now(),
                ]
            );
            
            // Marcar equipo como completo
            $team->update(['is_squad_complete' => true]);
            
            // Asignar capitanes aleatorios (primeros 2 seleccionados)
            $captainData = [
                'captain_id' => $selectedPlayers[0]['player_id'] ?? null,
                'vice_captain_id' => $selectedPlayers[1]['player_id'] ?? null,
            ];
            
            // Generar roster para GW1
            $rosterService = app(RosterGeneratorService::class);
            $rosters = $rosterService->generateFromDraft($team, $draft, $captainData);
            
            Log::info("Plantilla auto-asignada exitosamente para equipo: {$team->id}");
            
            return [
                'success' => true,
                'auto_assigned' => true,
                'team' => $team->fresh(),
                'draft' => $draft,
                'rosters' => $rosters,
                'budget_used' => SquadBuilderService::INITIAL_BUDGET - $budget,
                'budget_remaining' => $budget,
            ];
        });
    }
    
    /**
     * Seleccionar jugadores por posición ordenados por precio (más baratos primero)
     */
    protected function selectPlayersByPosition(int $position, int $count, int $seasonId, float $availableBudget): array
    {
        // Obtener jugadores activos de la posición
        $players = Player::where('is_active', true)
                         ->where('position', $position)
                         ->get();
        
        // Agregar precio a cada jugador
        $playersWithPrice = $players->map(function ($player) use ($seasonId) {
            $valuation = PlayerValuation::where('player_id', $player->id)
                                        ->where('season_id', $seasonId)
                                        ->first();
            
            $player->price = $valuation ? (float) $valuation->market_value : 5.00;
            return $player;
        });
        
        // Ordenar por precio ascendente (más baratos primero)
        $sortedPlayers = $playersWithPrice->sortBy('price')->values();
        
        $selected = [];
        $budgetUsed = 0;
        
        // Seleccionar los más baratos que quepan en el presupuesto
        foreach ($sortedPlayers as $player) {
            if (count($selected) >= $count) {
                break;
            }
            
            if ($budgetUsed + $player->price <= $availableBudget) {
                $selected[] = [
                    'player_id' => $player->id,
                    'position' => $position,
                    'price' => $player->price,
                    'added_at' => now()->toISOString(),
                ];
                $budgetUsed += $player->price;
            }
        }
        
        // Si no se pudo completar la cantidad requerida, tomar lo que se pueda
        if (count($selected) < $count) {
            Log::warning("No se pudieron seleccionar {$count} jugadores de posición {$position}. Solo se seleccionaron " . count($selected));
        }
        
        return $selected;
    }
    
    /**
     * Verificar si un equipo necesita auto-asignación
     * (deadline vencido y plantilla incompleta)
     */
    public function needsAutoAssignment(FantasyTeam $team): bool
    {
        // Si ya completó la plantilla, no necesita auto-asignación
        if ($team->is_squad_complete) {
            return false;
        }
        
        // Obtener el member del equipo para verificar deadline
        $member = $team->league->members()
                       ->where('user_id', $team->user_id)
                       ->first();
        
        if (!$member || !$member->squad_deadline_at) {
            return false;
        }
        
        // Verificar si el deadline ya venció
        return now()->isAfter($member->squad_deadline_at);
    }
    
    /**
     * Procesar auto-asignación masiva para equipos con deadline vencido
     */
    public function processExpiredDeadlines(): array
    {
        $results = [
            'processed' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];
        
        // Obtener equipos que necesitan auto-asignación
        $teams = FantasyTeam::where('is_squad_complete', false)
                            ->where('is_bot', false)
                            ->whereNotNull('league_id')
                            ->whereHas('league.members', function($query) {
                                $query->whereNotNull('squad_deadline_at')
                                      ->where('squad_deadline_at', '<', now());
                            })
                            ->get();
        
        Log::info("Procesando auto-asignación para {$teams->count()} equipos con deadline vencido");
        
        foreach ($teams as $team) {
            $results['processed']++;
            
            try {
                $this->autoAssignSquad($team);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'team_id' => $team->id,
                    'error' => $e->getMessage(),
                ];
                Log::error("Error en auto-asignación para equipo {$team->id}: " . $e->getMessage());
            }
        }
        
        Log::info("Auto-asignación masiva completada. Exitosos: {$results['success']}, Fallidos: {$results['failed']}");
        
        return $results;
    }
    
    /**
     * Asignar plantilla para equipo bot
     * Similar a auto-asignación pero más inteligente (mejores jugadores)
     */
    public function assignBotSquad(FantasyTeam $botTeam): array
    {
        if (!$botTeam->is_bot) {
            throw new \InvalidArgumentException('Este método es solo para equipos bot.');
        }
        
        return DB::transaction(function () use ($botTeam) {
            $seasonId = $botTeam->league->season_id;
            $budget = SquadBuilderService::INITIAL_BUDGET;
            $selectedPlayers = [];
            
            Log::info("Asignando plantilla para equipo bot: {$botTeam->id}");
            
            // Distribución balanceada para bots
            $positionsToSelect = [
                Player::POSITION_GK => 2,
                Player::POSITION_DF => 6,
                Player::POSITION_MF => 8,
                Player::POSITION_FW => 7,
            ];
            
            foreach ($positionsToSelect as $position => $count) {
                $selected = $this->selectBalancedPlayers($position, $count, $seasonId, $budget);
                
                foreach ($selected as $playerData) {
                    $selectedPlayers[] = $playerData;
                    $budget -= $playerData['price'];
                }
            }
            
            // Crear borrador completo
            $draft = SquadDraft::updateOrCreate(
                ['fantasy_team_id' => $botTeam->id],
                [
                    'selected_players' => $selectedPlayers,
                    'budget_spent' => SquadBuilderService::INITIAL_BUDGET - $budget,
                    'budget_remaining' => $budget,
                    'limits' => [
                        1 => $positionsToSelect[Player::POSITION_GK],
                        2 => $positionsToSelect[Player::POSITION_DF],
                        3 => $positionsToSelect[Player::POSITION_MF],
                        4 => $positionsToSelect[Player::POSITION_FW],
                    ],
                    'is_completed' => true,
                    'completed_at' => now(),
                ]
            );
            
            $botTeam->update(['is_squad_complete' => true]);
            
            // Asignar capitanes (los 2 jugadores más caros)
            $sortedByPrice = collect($selectedPlayers)->sortByDesc('price')->values();
            $captainData = [
                'captain_id' => $sortedByPrice[0]['player_id'] ?? null,
                'vice_captain_id' => $sortedByPrice[1]['player_id'] ?? null,
            ];
            
            $rosterService = app(RosterGeneratorService::class);
            $rosters = $rosterService->generateFromDraft($botTeam, $draft, $captainData);
            
            Log::info("Plantilla bot asignada exitosamente para equipo: {$botTeam->id}");
            
            return [
                'success' => true,
                'team' => $botTeam->fresh(),
                'draft' => $draft,
                'rosters' => $rosters,
            ];
        });
    }
    
    /**
     * Seleccionar jugadores balanceados (mezcla de baratos y buenos)
     * Para equipos bot
     */
    protected function selectBalancedPlayers(int $position, int $count, int $seasonId, float $availableBudget): array
    {
        $players = Player::where('is_active', true)
                         ->where('position', $position)
                         ->get();
        
        $playersWithPrice = $players->map(function ($player) use ($seasonId) {
            $valuation = PlayerValuation::where('player_id', $player->id)
                                        ->where('season_id', $seasonId)
                                        ->first();
            
            $player->price = $valuation ? (float) $valuation->market_value : 5.00;
            return $player;
        });
        
        // Ordenar aleatoriamente para diversidad
        $shuffledPlayers = $playersWithPrice->shuffle();
        
        $selected = [];
        $budgetUsed = 0;
        
        foreach ($shuffledPlayers as $player) {
            if (count($selected) >= $count) {
                break;
            }
            
            if ($budgetUsed + $player->price <= $availableBudget) {
                $selected[] = [
                    'player_id' => $player->id,
                    'position' => $position,
                    'price' => $player->price,
                    'added_at' => now()->toISOString(),
                ];
                $budgetUsed += $player->price;
            }
        }
        
        return $selected;
    }
}