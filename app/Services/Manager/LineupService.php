<?php

namespace App\Services\Manager;

use App\Models\FantasyTeam;
use App\Models\FantasyRoster;
use App\Models\Gameweek;
use App\Models\Player;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LineupService
{
    /**
     * Límites mínimos para una formación válida
     * (11 titulares deben cumplir estos mínimos)
     */
    const FORMATION_MINIMUMS = [
        Player::POSITION_GK => 1,  // Mínimo 1 arquero
        Player::POSITION_DF => 3,  // Mínimo 3 defensores
        Player::POSITION_MF => 2,  // Mínimo 2 mediocampistas
        Player::POSITION_FW => 1,  // Mínimo 1 delantero
    ];

    /**
     * Obtener alineación completa de un equipo para una gameweek
     */
    public function getLineup(FantasyTeam $team, int $gameweekId): array
    {
        $roster = FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $gameweekId)
            ->with('player')
            ->orderBy('slot')
            ->get();

        return [
            'starters' => $roster->where('is_starter', true)->values(),
            'bench' => $roster->where('is_starter', false)->values(),
            'captain' => $roster->firstWhere('captaincy', FantasyRoster::CAPTAINCY_CAPTAIN),
            'vice_captain' => $roster->firstWhere('captaincy', FantasyRoster::CAPTAINCY_VICE),
            'all' => $roster,
        ];
    }

    /**
     * Intercambiar dos jugadores (titular ↔ suplente)
     */
    public function swapPlayers(
        FantasyTeam $team,
        int $gameweekId,
        int $player1Id,
        int $player2Id
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $player1Id, $player2Id) {
            // Validar que se puede editar
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            // Obtener ambos rosters
            $roster1 = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $player1Id)
                ->with('player')
                ->firstOrFail();

            $roster2 = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $player2Id)
                ->with('player')
                ->firstOrFail();

            // Intercambiar posiciones
            $tempSlot = $roster1->slot;
            $tempIsStarter = $roster1->is_starter;
            $tempCaptaincy = $roster1->captaincy;

            $roster1->update([
                'slot' => $roster2->slot,
                'is_starter' => $roster2->is_starter,
                'captaincy' => $roster2->captaincy,
            ]);

            $roster2->update([
                'slot' => $tempSlot,
                'is_starter' => $tempIsStarter,
                'captaincy' => $tempCaptaincy,
            ]);

            // Validar formación resultante solo si hay 11 titulares
            $allStarters = $this->getStarters($team, $gameweekId);
            if ($allStarters->count() === 11) {
                $this->validateFormation($allStarters);
            }

            return [
                'success' => true,
                'roster1' => $roster1->fresh(),
                'roster2' => $roster2->fresh(),
                'message' => __('Jugadores intercambiados correctamente.'),
            ];
        });
    }

    /**
     * Mover jugador a titulares
     */
    public function moveToStarting(
        FantasyTeam $team,
        int $gameweekId,
        int $playerId,
        int $targetSlot
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $playerId, $targetSlot) {
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            $roster = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $playerId)
                ->with('player')
                ->firstOrFail();

            // Si no es titular, validar que haya espacio
            if (!$roster->is_starter) {
                $startersCount = FantasyRoster::where('fantasy_team_id', $team->id)
                    ->where('gameweek_id', $gameweekId)
                    ->where('is_starter', true)
                    ->count();
                
                if ($startersCount >= 11) {
                    throw ValidationException::withMessages([
                        'starters' => __('Ya tienes 11 titulares. Debes mover uno al banco primero.'),
                    ]);
                }
                
                // Validar límites de posición
                $this->validatePositionLimit($team, $gameweekId, $roster->player->position);
            }

            // Si ya es titular, solo cambiar slot
            if ($roster->is_starter) {
                $roster->update(['slot' => $targetSlot]);
            } else {
                // Mover al banco al jugador que ocupa el slot objetivo
                $currentStarter = FantasyRoster::where('fantasy_team_id', $team->id)
                    ->where('gameweek_id', $gameweekId)
                    ->where('slot', $targetSlot)
                    ->where('is_starter', true)
                    ->first();

                if ($currentStarter) {
                    // Encontrar primer slot disponible en banco (12-34)
                    $benchSlot = $this->findAvailableBenchSlot($team, $gameweekId);
                    
                    $currentStarter->update([
                        'slot' => $benchSlot,
                        'is_starter' => false,
                        'captaincy' => FantasyRoster::CAPTAINCY_NONE,
                    ]);
                }

                // Mover a titulares
                $roster->update([
                    'slot' => $targetSlot,
                    'is_starter' => true,
                ]);
            }

            // Validar formación solo si hay 11 titulares
            $allStarters = $this->getStarters($team, $gameweekId);
            if ($allStarters->count() === 11) {
                $this->validateFormation($allStarters);
            }

            return [
                'success' => true,
                'roster' => $roster->fresh(),
                'message' => __('Jugador movido a titulares.'),
            ];
        });
    }

    /**
     * Validar que no se exceda el máximo de jugadores por posición en titulares
     */
    protected function validatePositionLimit(FantasyTeam $team, int $gameweekId, int $position): void
    {
        $starters = $this->getStarters($team, $gameweekId);
        
        // Contar cuántos hay actualmente en esa posición
        $currentCount = $starters->filter(function ($roster) use ($position) {
            return $roster->player->position === $position;
        })->count();

        // Definir máximos por posición
        $maxByPosition = [
            Player::POSITION_GK => 1,  // Máximo 1 arquero
            Player::POSITION_DF => 5,  // Máximo 5 defensores
            Player::POSITION_MF => 5,  // Máximo 5 mediocampistas
            Player::POSITION_FW => 3,  // Máximo 3 delanteros
        ];

        $max = $maxByPosition[$position] ?? 11;

        if ($currentCount >= $max) {
            $positionName = Player::POSITIONS[$position];
            throw ValidationException::withMessages([
                'position' => __('No puedes agregar más :position. Máximo permitido: :max.', [
                    'position' => $positionName,
                    'max' => $max,
                ]),
            ]);
        }
    }

    /**
     * Mover jugador al banco
     */
    public function moveToBench(
        FantasyTeam $team,
        int $gameweekId,
        int $playerId,
        int $targetSlot
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $playerId, $targetSlot) {
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            $roster = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $playerId)
                ->with('player')
                ->firstOrFail();

            // Mover al banco al jugador que ocupa el slot objetivo
            $currentBench = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('slot', $targetSlot)
                ->where('is_starter', false)
                ->first();

            if ($currentBench) {
                // Encontrar primer slot disponible (en titulares o banco según origen)
                if ($roster->is_starter) {
                    // Buscar slot en titulares
                    $newSlot = $this->findAvailableStarterSlot($team, $gameweekId);
                    $currentBench->update([
                        'slot' => $newSlot,
                        'is_starter' => true,
                    ]);
                } else {
                    // Buscar slot en banco
                    $newSlot = $this->findAvailableBenchSlot($team, $gameweekId);
                    $currentBench->update(['slot' => $newSlot]);
                }
            }

            // Mover al banco (quitar capitanía si la tenía)
            $roster->update([
                'slot' => $targetSlot,
                'is_starter' => false,
                'captaincy' => FantasyRoster::CAPTAINCY_NONE,
            ]);

            return [
                'success' => true,
                'roster' => $roster->fresh(),
                'message' => __('Jugador movido al banco.'),
            ];
        });
    }

    /**
     * Asignar capitán
     */
    public function assignCaptain(
        FantasyTeam $team,
        int $gameweekId,
        int $playerId
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $playerId) {
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            $roster = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $playerId)
                ->with('player')
                ->firstOrFail();

            // Validar que es titular
            if (!$roster->is_starter) {
                throw ValidationException::withMessages([
                    'captain' => __('El capitán debe ser titular.'),
                ]);
            }

            // Quitar capitanía al actual capitán
            FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('captaincy', FantasyRoster::CAPTAINCY_CAPTAIN)
                ->update(['captaincy' => FantasyRoster::CAPTAINCY_NONE]);

            // Asignar nuevo capitán
            $roster->update(['captaincy' => FantasyRoster::CAPTAINCY_CAPTAIN]);

            return [
                'success' => true,
                'roster' => $roster->fresh(),
                'message' => __('Capitán asignado correctamente.'),
            ];
        });
    }

    /**
     * Asignar vicecapitán
     */
    public function assignViceCaptain(
        FantasyTeam $team,
        int $gameweekId,
        int $playerId
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $playerId) {
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            $roster = FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('player_id', $playerId)
                ->with('player')
                ->firstOrFail();

            // Validar que es titular
            if (!$roster->is_starter) {
                throw ValidationException::withMessages([
                    'vice_captain' => __('El vicecapitán debe ser titular.'),
                ]);
            }

            // Quitar vicecapitanía al actual vicecapitán
            FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('gameweek_id', $gameweekId)
                ->where('captaincy', FantasyRoster::CAPTAINCY_VICE)
                ->update(['captaincy' => FantasyRoster::CAPTAINCY_NONE]);

            // Asignar nuevo vicecapitán
            $roster->update(['captaincy' => FantasyRoster::CAPTAINCY_VICE]);

            return [
                'success' => true,
                'roster' => $roster->fresh(),
                'message' => __('Vicecapitán asignado correctamente.'),
            ];
        });
    }

    /**
     * Guardar todos los cambios de alineación
     */
    public function saveLineup(
        FantasyTeam $team,
        int $gameweekId,
        array $lineupData
    ): array {
        return DB::transaction(function () use ($team, $gameweekId, $lineupData) {
            $gameweek = Gameweek::findOrFail($gameweekId);
            $this->ensureCanEditLineup($gameweek);

            $updatedRosters = [];

            // Actualizar cada jugador según lineupData
            foreach ($lineupData as $data) {
                $roster = FantasyRoster::where('fantasy_team_id', $team->id)
                    ->where('gameweek_id', $gameweekId)
                    ->where('player_id', $data['player_id'])
                    ->firstOrFail();

                $roster->update([
                    'slot' => $data['slot'],
                    'is_starter' => $data['is_starter'],
                    'captaincy' => $data['captaincy'] ?? FantasyRoster::CAPTAINCY_NONE,
                ]);

                $updatedRosters[] = $roster->fresh();
            }

            // Validar alineación completa
            $starters = collect($updatedRosters)->where('is_starter', true);
            
            if ($starters->count() === 11) {
                $this->validateFormation($starters);
            }

            // Validar capitanes
            $captainCount = collect($updatedRosters)
                ->where('captaincy', FantasyRoster::CAPTAINCY_CAPTAIN)
                ->count();
            
            $viceCount = collect($updatedRosters)
                ->where('captaincy', FantasyRoster::CAPTAINCY_VICE)
                ->count();

            if ($captainCount !== 1) {
                throw ValidationException::withMessages([
                    'captain' => __('Debes tener exactamente 1 capitán.'),
                ]);
            }

            if ($viceCount !== 1) {
                throw ValidationException::withMessages([
                    'vice_captain' => __('Debes tener exactamente 1 vicecapitán.'),
                ]);
            }

            return [
                'success' => true,
                'rosters' => $updatedRosters,
                'message' => __('Alineación guardada correctamente.'),
            ];
        });
    }

    /**
     * Obtener estadísticas de la alineación
     */
    public function getLineupStats(FantasyTeam $team, int $gameweekId): array
    {
        $lineup = $this->getLineup($team, $gameweekId);
        
        $startersPositions = $lineup['starters']->groupBy(function ($roster) {
            return $roster->player->position;
        })->map->count();

        return [
            'total_players' => $lineup['all']->count(),
            'starters_count' => $lineup['starters']->count(),
            'bench_count' => $lineup['bench']->count(),
            'has_captain' => $lineup['captain'] !== null,
            'has_vice_captain' => $lineup['vice_captain'] !== null,
            'formation' => [
                'GK' => $startersPositions[Player::POSITION_GK] ?? 0,
                'DF' => $startersPositions[Player::POSITION_DF] ?? 0,
                'MF' => $startersPositions[Player::POSITION_MF] ?? 0,
                'FW' => $startersPositions[Player::POSITION_FW] ?? 0,
            ],
            'is_valid' => $this->isLineupValid($lineup['starters'], $lineup['captain'], $lineup['vice_captain']),
        ];
    }

    /**
     * Verificar si la alineación es válida
     */
    protected function isLineupValid($starters, $captain, $viceCaptain): bool
    {
        if ($starters->count() !== 11) {
            return false;
        }

        try {
            $this->validateFormation($starters);
            return $captain !== null && $viceCaptain !== null;
        } catch (ValidationException $e) {
            return false;
        }
    }

    /**
     * Validar formación (11 titulares con mínimos correctos)
     */
    public function validateFormation(Collection $starters): void
    {
        // Validar que hay 11 titulares
        if ($starters->count() !== 11) {
            throw ValidationException::withMessages([
                'formation' => __('Debes tener exactamente 11 titulares.'),
            ]);
        }

        // Contar por posición
        $positionCounts = [
            Player::POSITION_GK => 0,
            Player::POSITION_DF => 0,
            Player::POSITION_MF => 0,
            Player::POSITION_FW => 0,
        ];

        foreach ($starters as $roster) {
            $position = $roster->player->position;
            $positionCounts[$position] = ($positionCounts[$position] ?? 0) + 1;
        }

        // Validar mínimos
        foreach (self::FORMATION_MINIMUMS as $position => $minimum) {
            if ($positionCounts[$position] < $minimum) {
                $positionName = Player::POSITIONS[$position];
                throw ValidationException::withMessages([
                    'formation' => __('Formación inválida: mínimo :min :position.', [
                        'min' => $minimum,
                        'position' => $positionName,
                    ]),
                ]);
            }
        }
    }

    /**
     * Verificar si se puede editar la alineación
     * 
     * TESTING MODE: Si está habilitado, permite editar siempre
     */
    public function canEditLineup(Gameweek $gameweek): bool
    {
        // Si el modo testing está habilitado, permitir siempre
        if (config('lineup.testing_mode', false) || config('lineup.allow_edit_after_deadline', false)) {
            return true;
        }

        return !$gameweek->hasStarted();
    }

    /**
     * Asegurar que se puede editar (lanza excepción si no)
     */
    protected function ensureCanEditLineup(Gameweek $gameweek): void
    {
        if (!$this->canEditLineup($gameweek)) {
            throw ValidationException::withMessages([
                'gameweek' => __('La gameweek ya ha iniciado. No puedes editar la alineación.'),
            ]);
        }
    }

    /**
     * Obtener titulares
     */
    protected function getStarters(FantasyTeam $team, int $gameweekId): Collection
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $gameweekId)
            ->where('is_starter', true)
            ->with('player')
            ->get();
    }

    /**
     * Encontrar slot disponible en titulares (1-11)
     */
    protected function findAvailableStarterSlot(FantasyTeam $team, int $gameweekId): int
    {
        $usedSlots = FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $gameweekId)
            ->where('is_starter', true)
            ->pluck('slot')
            ->toArray();

        // Buscar primer slot libre entre 1 y 11
        for ($slot = 1; $slot <= 11; $slot++) {
            if (!in_array($slot, $usedSlots)) {
                return $slot;
            }
        }

        // Si no hay slots libres, forzar slot 1 (no debe pasar si validamos antes)
        return 1;
    }

    /**
     * Encontrar slot disponible en banco (12-34 para 23 jugadores)
     */
    protected function findAvailableBenchSlot(FantasyTeam $team, int $gameweekId): int
    {
        $usedSlots = FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $gameweekId)
            ->where('is_starter', false)
            ->pluck('slot')
            ->toArray();

        // Buscar primer slot libre entre 12 y 34 (23 espacios para banco)
        for ($slot = 12; $slot <= 34; $slot++) {
            if (!in_array($slot, $usedSlots)) {
                return $slot;
            }
        }

        return 12;
    }
}