<?php

namespace App\Services\Manager;

use App\Models\SquadDraft;
use App\Models\Player;
use Illuminate\Validation\ValidationException;

class SquadValidationService
{
    /**
     * Validar agregar jugador
     */
    public function validateAddPlayer(SquadDraft $draft, Player $player, int $position, int $seasonId): void
    {
        // 1. Validar que el jugador está activo
        if (!$player->is_active) {
            throw ValidationException::withMessages([
                'player' => __('El jugador no está disponible.'),
            ]);
        }
        
        // 2. Validar que la posición corresponde al jugador
        if ($player->position !== $position) {
            throw ValidationException::withMessages([
                'position' => __('La posición no corresponde a este jugador.'),
            ]);
        }
        
        // 3. Validar que no está ya seleccionado
        if ($draft->hasPlayer($player->id)) {
            throw ValidationException::withMessages([
                'player' => __('Ya tienes este jugador en tu plantilla.'),
            ]);
        }
        
        // 4. Validar límite total (23)
        if ($draft->player_count >= SquadBuilderService::SQUAD_SIZE) {
            throw ValidationException::withMessages([
                'squad' => __('Ya completaste los :max jugadores.', [
                    'max' => SquadBuilderService::SQUAD_SIZE
                ]),
            ]);
        }
        
        // 5. Validar límite por posición
        $currentCount = $draft->getCountByPosition($position);
        $maxAllowed = SquadBuilderService::POSITION_LIMITS[$position]['max'];
        
        if ($currentCount >= $maxAllowed) {
            $positionName = Player::POSITIONS[$position] ?? '';
            throw ValidationException::withMessages([
                'position' => __('Alcanzaste el máximo de :max jugadores en :pos.', [
                    'max' => $maxAllowed,
                    'pos' => $positionName,
                ]),
            ]);
        }
        
        // 6. Validar presupuesto
        $squadBuilder = app(SquadBuilderService::class);
        $price = $squadBuilder->getPlayerPrice($player, $seasonId);
        
        if ($draft->budget_remaining < $price) {
            $shortage = $price - $draft->budget_remaining;
            throw ValidationException::withMessages([
                'budget' => __('Presupuesto insuficiente. Te faltan $:amount', [
                    'amount' => number_format($shortage, 2)
                ]),
            ]);
        }
    }
    
    /**
     * Validar armado completo
     */
    public function validateCompleteSquad(SquadDraft $draft): void
    {
        $selectedPlayers = $draft->selected_players ?? [];
        $playerCount = count($selectedPlayers);
        
        // 1. Validar cantidad total (23)
        if ($playerCount !== SquadBuilderService::SQUAD_SIZE) {
            throw ValidationException::withMessages([
                'squad' => __('Debes completar los :required jugadores. Tienes :current.', [
                    'required' => SquadBuilderService::SQUAD_SIZE,
                    'current' => $playerCount,
                ]),
            ]);
        }
        
        // 2. Validar mínimos por posición
        $limits = $draft->limits ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        foreach (SquadBuilderService::POSITION_LIMITS as $position => $rules) {
            $current = $limits[$position] ?? 0;
            
            if ($current < $rules['min']) {
                $positionName = Player::POSITIONS[$position] ?? '';
                throw ValidationException::withMessages([
                    'position' => __('Faltan jugadores. Mínimo :min en :pos (tienes :current).', [
                        'min' => $rules['min'],
                        'pos' => $positionName,
                        'current' => $current,
                    ]),
                ]);
            }
        }
        
        // 3. Validar que no haya duplicados
        $playerIds = collect($selectedPlayers)->pluck('player_id')->toArray();
        $uniqueIds = array_unique($playerIds);
        
        if (count($playerIds) !== count($uniqueIds)) {
            throw ValidationException::withMessages([
                'squad' => __('Hay jugadores duplicados en tu plantilla.'),
            ]);
        }
    }
    
    /**
     * Validar que el equipo puede ser activado
     */
    public function canActivateTeam(SquadDraft $draft): bool
    {
        try {
            $this->validateCompleteSquad($draft);
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }
    
    /**
     * Obtener errores de validación sin lanzar excepción
     */
    public function getValidationErrors(SquadDraft $draft): array
    {
        $errors = [];
        
        // Validar cantidad total
        $playerCount = $draft->player_count;
        if ($playerCount < SquadBuilderService::SQUAD_SIZE) {
            $errors[] = __('Faltan :count jugadores.', [
                'count' => SquadBuilderService::SQUAD_SIZE - $playerCount
            ]);
        }
        
        // Validar mínimos por posición
        $limits = $draft->limits ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        foreach (SquadBuilderService::POSITION_LIMITS as $position => $rules) {
            $current = $limits[$position] ?? 0;
            
            if ($current < $rules['min']) {
                $positionName = Player::POSITIONS[$position] ?? '';
                $errors[] = __('Faltan :count :pos (mínimo :min).', [
                    'count' => $rules['min'] - $current,
                    'pos' => $positionName,
                    'min' => $rules['min'],
                ]);
            }
        }
        
        return $errors;
    }
    
    /**
     * Obtener resumen del estado del borrador
     */
    public function getDraftSummary(SquadDraft $draft): array
    {
        $limits = $draft->limits ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        return [
            'total' => [
                'current' => $draft->player_count,
                'required' => SquadBuilderService::SQUAD_SIZE,
                'remaining' => SquadBuilderService::SQUAD_SIZE - $draft->player_count,
                'is_complete' => $draft->player_count === SquadBuilderService::SQUAD_SIZE,
            ],
            'positions' => [
                'GK' => [
                    'current' => $limits[1] ?? 0,
                    'min' => SquadBuilderService::POSITION_LIMITS[1]['min'],
                    'max' => SquadBuilderService::POSITION_LIMITS[1]['max'],
                    'remaining' => SquadBuilderService::POSITION_LIMITS[1]['max'] - ($limits[1] ?? 0),
                    'meets_minimum' => ($limits[1] ?? 0) >= SquadBuilderService::POSITION_LIMITS[1]['min'],
                ],
                'DF' => [
                    'current' => $limits[2] ?? 0,
                    'min' => SquadBuilderService::POSITION_LIMITS[2]['min'],
                    'max' => SquadBuilderService::POSITION_LIMITS[2]['max'],
                    'remaining' => SquadBuilderService::POSITION_LIMITS[2]['max'] - ($limits[2] ?? 0),
                    'meets_minimum' => ($limits[2] ?? 0) >= SquadBuilderService::POSITION_LIMITS[2]['min'],
                ],
                'MF' => [
                    'current' => $limits[3] ?? 0,
                    'min' => SquadBuilderService::POSITION_LIMITS[3]['min'],
                    'max' => SquadBuilderService::POSITION_LIMITS[3]['max'],
                    'remaining' => SquadBuilderService::POSITION_LIMITS[3]['max'] - ($limits[3] ?? 0),
                    'meets_minimum' => ($limits[3] ?? 0) >= SquadBuilderService::POSITION_LIMITS[3]['min'],
                ],
                'FW' => [
                    'current' => $limits[4] ?? 0,
                    'min' => SquadBuilderService::POSITION_LIMITS[4]['min'],
                    'max' => SquadBuilderService::POSITION_LIMITS[4]['max'],
                    'remaining' => SquadBuilderService::POSITION_LIMITS[4]['max'] - ($limits[4] ?? 0),
                    'meets_minimum' => ($limits[4] ?? 0) >= SquadBuilderService::POSITION_LIMITS[4]['min'],
                ],
            ],
            'budget' => [
                'initial' => SquadBuilderService::INITIAL_BUDGET,
                'spent' => (float) $draft->budget_spent,
                'remaining' => (float) $draft->budget_remaining,
                'percentage_used' => $draft->budget_spent > 0 
                    ? round(($draft->budget_spent / SquadBuilderService::INITIAL_BUDGET) * 100, 2)
                    : 0,
            ],
            'can_complete' => $this->canActivateTeam($draft),
            'errors' => $this->getValidationErrors($draft),
        ];
    }
}