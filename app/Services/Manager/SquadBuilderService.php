<?php

namespace App\Services\Manager;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\SquadDraft;
use App\Models\PlayerValuation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SquadBuilderService
{
    /**
     * Constantes de límites
     */
    const SQUAD_SIZE = 23;
    const STARTERS_SIZE = 11;
    const INITIAL_BUDGET = 200.00;
    
    const POSITION_LIMITS = [
        1 => ['min' => 1, 'max' => 3],  // GK
        2 => ['min' => 3, 'max' => 8],  // DF
        3 => ['min' => 3, 'max' => 8],  // MF
        4 => ['min' => 1, 'max' => 4],  // FW
    ];
    
    /**
     * Agregar jugador al borrador
     */
    public function addPlayer(FantasyTeam $team, Player $player, int $position): array
    {
        return DB::transaction(function () use ($team, $player, $position) {
            $draft = $this->getOrCreateDraft($team);
            
            // Validar que se puede agregar (usará el servicio de validación)
            $validationService = app(SquadValidationService::class);
            $validationService->validateAddPlayer($draft, $player, $position, $team->league->season_id);
            
            // Obtener precio del jugador
            $price = $this->getPlayerPrice($player, $team->league->season_id);
            
            // Actualizar borrador
            $selectedPlayers = $draft->selected_players ?? [];
            $selectedPlayers[] = [
                'player_id' => $player->id,
                'position' => $position,
                'price' => $price,
                'added_at' => now()->toISOString(),
            ];
            
            $budgetSpent = $draft->budget_spent + $price;
            $budgetRemaining = self::INITIAL_BUDGET - $budgetSpent;
            
            // Actualizar contadores por posición
            $limits = $draft->limits ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $limits[$position] = ($limits[$position] ?? 0) + 1;
            
            $draft->update([
                'selected_players' => $selectedPlayers,
                'budget_spent' => $budgetSpent,
                'budget_remaining' => $budgetRemaining,
                'limits' => $limits,
            ]);
            
            return [
                'success' => true,
                'draft' => $draft->fresh(),
                'player' => $player,
                'message' => __('Jugador agregado correctamente.'),
            ];
        });
    }
    
    /**
     * Remover jugador del borrador
     */
    public function removePlayer(FantasyTeam $team, int $playerId): array
    {
        return DB::transaction(function () use ($team, $playerId) {
            $draft = $this->getDraft($team);
            
            if (!$draft) {
                throw ValidationException::withMessages([
                    'draft' => __('No se encontró borrador activo.'),
                ]);
            }
            
            $selectedPlayers = $draft->selected_players ?? [];
            $playerIndex = collect($selectedPlayers)->search(fn($p) => $p['player_id'] == $playerId);
            
            if ($playerIndex === false) {
                throw ValidationException::withMessages([
                    'player' => __('El jugador no está en tu plantilla.'),
                ]);
            }
            
            $removedPlayer = $selectedPlayers[$playerIndex];
            unset($selectedPlayers[$playerIndex]);
            $selectedPlayers = array_values($selectedPlayers); // Reindexar
            
            // Actualizar presupuesto y límites
            $budgetSpent = $draft->budget_spent - $removedPlayer['price'];
            $budgetRemaining = self::INITIAL_BUDGET - $budgetSpent;
            
            $limits = $draft->limits;
            $limits[$removedPlayer['position']]--;
            
            $draft->update([
                'selected_players' => $selectedPlayers,
                'budget_spent' => $budgetSpent,
                'budget_remaining' => $budgetRemaining,
                'limits' => $limits,
            ]);
            
            return [
                'success' => true,
                'draft' => $draft->fresh(),
                'message' => __('Jugador removido correctamente.'),
            ];
        });
    }
    
    /**
 * Completar armado de plantilla
 */
public function completeSquad(FantasyTeam $team, array $captainData): array
{
    return DB::transaction(function () use ($team, $captainData) {
        $draft = $this->getDraft($team);
        
        if (!$draft) {
            throw ValidationException::withMessages([
                'draft' => __('No se encontró borrador activo.'),
            ]);
        }
        
        // Validar que está completo
        $validationService = app(SquadValidationService::class);
        $validationService->validateCompleteSquad($draft);
        
        // Validar capitanes
        $this->validateCaptains($draft, $captainData);
        
        // Guardar capitanes en el draft
        $draft->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        
        // Marcar equipo como completo
        $team->update(['is_squad_complete' => true]);
        
        // Limpiar deadline del LeagueMember
        $leagueMember = \App\Models\LeagueMember::where('league_id', $team->league_id)
            ->where('user_id', $team->user_id)
            ->first();
        
        if ($leagueMember) {
            \Log::info('Limpiando deadline', [
                'league_member_id' => $leagueMember->id,
                'old_deadline' => $leagueMember->squad_deadline_at,
            ]);
            
            $leagueMember->update(['squad_deadline_at' => null]);
            
            \Log::info('Deadline limpiado', [
                'league_member_id' => $leagueMember->id,
                'new_deadline' => $leagueMember->fresh()->squad_deadline_at,
            ]);
        } else {
            \Log::warning('LeagueMember no encontrado', [
                'team_id' => $team->id,
                'league_id' => $team->league_id,
                'user_id' => $team->user_id,
            ]);
        }
        
        return [
            'success' => true,
            'team' => $team->fresh(),
            'message' => __('¡Plantilla completada!'),
        ];
    });
}
    
    /**
     * Actualizar paso actual del wizard
     */
    public function updateStep(FantasyTeam $team, int $step): void
    {
        $draft = $this->getOrCreateDraft($team);
        
        if ($step < 1 || $step > 5) {
            throw ValidationException::withMessages([
                'step' => __('Paso inválido.'),
            ]);
        }
        
        $draft->update(['current_step' => $step]);
    }
    
    /**
     * Obtener o crear borrador
     */
    public function getOrCreateDraft(FantasyTeam $team): SquadDraft
    {
        return SquadDraft::firstOrCreate(
            ['fantasy_team_id' => $team->id],
            [
                'selected_players' => [],
                'current_step' => 1,
                'budget_spent' => 0,
                'budget_remaining' => self::INITIAL_BUDGET,
                'limits' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                'is_completed' => false,
            ]
        );
    }
    
    /**
     * Obtener borrador existente
     */
    public function getDraft(FantasyTeam $team): ?SquadDraft
    {
        return SquadDraft::where('fantasy_team_id', $team->id)->first();
    }
    
    /**
     * Obtener precio del jugador
     */
    public function getPlayerPrice(Player $player, int $seasonId): float
    {
        $valuation = PlayerValuation::where('player_id', $player->id)
                                     ->where('season_id', $seasonId)
                                     ->first();
        
        return $valuation ? (float) $valuation->market_value : 5.00; // Default si no hay valuación
    }
    
    /**
     * Validar capitanes
     */
    protected function validateCaptains(SquadDraft $draft, array $captainData): void
    {
        $selectedPlayerIds = $draft->getSelectedPlayerIds();
        
        // Validar que el capitán esté en la plantilla
        if (!in_array($captainData['captain_id'], $selectedPlayerIds)) {
            throw ValidationException::withMessages([
                'captain' => __('El capitán debe estar en tu plantilla.'),
            ]);
        }
        
        // Validar que el vicecapitán esté en la plantilla
        if (!in_array($captainData['vice_captain_id'], $selectedPlayerIds)) {
            throw ValidationException::withMessages([
                'vice_captain' => __('El vicecapitán debe estar en tu plantilla.'),
            ]);
        }
        
        // Validar que sean diferentes
        if ($captainData['captain_id'] === $captainData['vice_captain_id']) {
            throw ValidationException::withMessages([
                'captains' => __('El capitán y vicecapitán deben ser diferentes.'),
            ]);
        }
    }
    
    /**
     * Obtener jugadores disponibles por posición
     */
    public function getAvailablePlayersByPosition(int $position, int $seasonId, ?SquadDraft $draft = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Player::where('is_active', true)
                       ->where('position', $position)
                       ->with(['realPlayer', 'valuations' => function($q) use ($seasonId) {
                           $q->where('season_id', $seasonId);
                       }]);
        
        // Excluir jugadores ya seleccionados
        if ($draft && $draft->selected_players) {
            $selectedIds = $draft->getSelectedPlayerIds();
            $query->whereNotIn('players.id', $selectedIds);
        }
        
        return $query->get();
    }
}