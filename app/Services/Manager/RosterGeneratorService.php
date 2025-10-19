<?php

namespace App\Services\Manager;

use App\Models\FantasyTeam;
use App\Models\SquadDraft;
use App\Models\FantasyRoster;
use App\Models\Gameweek;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RosterGeneratorService
{
    /**
     * Generar FantasyRoster para GW1 desde el borrador completado
     */
    public function generateFromDraft(FantasyTeam $team, SquadDraft $draft, array $captainData): array
    {
        return DB::transaction(function () use ($team, $draft, $captainData) {
            // Obtener GW1 de la temporada de la liga
            $gw1 = Gameweek::where('season_id', $team->league->season_id)
                           ->where('number', 1)
                           ->first();
            
            if (!$gw1) {
                throw ValidationException::withMessages([
                    'gameweek' => __('No se encontró la Gameweek 1 de la temporada.'),
                ]);
            }
            
            $selectedPlayers = $draft->selected_players ?? [];
            
            if (empty($selectedPlayers)) {
                throw ValidationException::withMessages([
                    'squad' => __('No hay jugadores seleccionados en el borrador.'),
                ]);
            }
            
            // Validar que no existan rosters previos para este equipo en GW1
            $existingRosters = FantasyRoster::where('fantasy_team_id', $team->id)
                                            ->where('gameweek_id', $gw1->id)
                                            ->exists();
            
            if ($existingRosters) {
                throw ValidationException::withMessages([
                    'roster' => __('Ya existe una alineación para la GW1.'),
                ]);
            }
            
            $rosters = [];
            $startersCount = 0;
            
            // Los primeros 11 seleccionados serán titulares
            foreach ($selectedPlayers as $index => $playerData) {
                $isStarter = $startersCount < SquadBuilderService::STARTERS_SIZE;
                
                // Determinar captaincy
                $captaincy = 0;
                if ($playerData['player_id'] == $captainData['captain_id']) {
                    $captaincy = 1; // Capitán
                } elseif ($playerData['player_id'] == $captainData['vice_captain_id']) {
                    $captaincy = 2; // Vicecapitán
                }
                
                // Crear registro en fantasy_rosters
                $roster = FantasyRoster::create([
                    'fantasy_team_id' => $team->id,
                    'player_id' => $playerData['player_id'],
                    'gameweek_id' => $gw1->id,
                    'slot' => $index + 1,
                    'is_starter' => $isStarter,
                    'captaincy' => $captaincy,
                ]);
                
                $rosters[] = $roster;
                
                if ($isStarter) {
                    $startersCount++;
                }
            }
            
            return $rosters;
        });
    }
    
    /**
     * Actualizar alineación para GW1 (antes de que inicie)
     * Permite al manager modificar titulares, suplentes y capitanes
     */
    public function updateRoster(FantasyTeam $team, int $gameweekId, array $rosterData): array
    {
        return DB::transaction(function () use ($team, $gameweekId, $rosterData) {
            // Validar que la gameweek aún no ha iniciado
            $gameweek = Gameweek::findOrFail($gameweekId);
            
            if ($gameweek->hasStarted()) {
                throw ValidationException::withMessages([
                    'gameweek' => __('No puedes modificar la alineación. La gameweek ya inició.'),
                ]);
            }
            
            // Validar datos de entrada
            $this->validateRosterData($rosterData);
            
            // Obtener rosters actuales
            $existingRosters = FantasyRoster::where('fantasy_team_id', $team->id)
                                            ->where('gameweek_id', $gameweekId)
                                            ->get();
            
            if ($existingRosters->isEmpty()) {
                throw ValidationException::withMessages([
                    'roster' => __('No se encontró alineación para esta gameweek.'),
                ]);
            }
            
            $updatedRosters = [];
            
            // Actualizar cada roster
            foreach ($rosterData as $playerData) {
                $roster = $existingRosters->firstWhere('player_id', $playerData['player_id']);
                
                if (!$roster) {
                    throw ValidationException::withMessages([
                        'player' => __('Jugador :id no encontrado en tu plantilla.', [
                            'id' => $playerData['player_id']
                        ]),
                    ]);
                }
                
                $roster->update([
                    'slot' => $playerData['slot'],
                    'is_starter' => $playerData['is_starter'],
                    'captaincy' => $playerData['captaincy'] ?? 0,
                ]);
                
                $updatedRosters[] = $roster->fresh();
            }
            
            return $updatedRosters;
        });
    }
    
    /**
     * Validar datos de alineación
     */
    protected function validateRosterData(array $rosterData): void
    {
        // Validar que hay exactamente 23 jugadores
        if (count($rosterData) !== SquadBuilderService::SQUAD_SIZE) {
            throw ValidationException::withMessages([
                'roster' => __('Debes tener :size jugadores en tu plantilla.', [
                    'size' => SquadBuilderService::SQUAD_SIZE
                ]),
            ]);
        }
        
        // Contar titulares
        $startersCount = collect($rosterData)->where('is_starter', true)->count();
        
        if ($startersCount !== SquadBuilderService::STARTERS_SIZE) {
            throw ValidationException::withMessages([
                'starters' => __('Debes tener exactamente :size titulares.', [
                    'size' => SquadBuilderService::STARTERS_SIZE
                ]),
            ]);
        }
        
        // Validar capitanes (debe haber 1 capitán y 1 vicecapitán)
        $captainCount = collect($rosterData)->where('captaincy', 1)->count();
        $viceCaptainCount = collect($rosterData)->where('captaincy', 2)->count();
        
        if ($captainCount !== 1) {
            throw ValidationException::withMessages([
                'captain' => __('Debes tener exactamente 1 capitán.'),
            ]);
        }
        
        if ($viceCaptainCount !== 1) {
            throw ValidationException::withMessages([
                'vice_captain' => __('Debes tener exactamente 1 vicecapitán.'),
            ]);
        }
        
        // Validar que capitán y vicecapitán sean titulares
        $captainData = collect($rosterData)->firstWhere('captaincy', 1);
        $viceCaptainData = collect($rosterData)->firstWhere('captaincy', 2);
        
        if ($captainData && !$captainData['is_starter']) {
            throw ValidationException::withMessages([
                'captain' => __('El capitán debe ser titular.'),
            ]);
        }
        
        if ($viceCaptainData && !$viceCaptainData['is_starter']) {
            throw ValidationException::withMessages([
                'vice_captain' => __('El vicecapitán debe ser titular.'),
            ]);
        }
    }
    
    /**
     * Obtener alineación actual de un equipo para una gameweek
     */
    public function getRoster(FantasyTeam $team, int $gameweekId): \Illuminate\Database\Eloquent\Collection
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $gameweekId)
                            ->with('player')
                            ->orderBy('slot')
                            ->get();
    }
    
    /**
     * Obtener titulares de un equipo para una gameweek
     */
    public function getStarters(FantasyTeam $team, int $gameweekId): \Illuminate\Database\Eloquent\Collection
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $gameweekId)
                            ->where('is_starter', true)
                            ->with('player')
                            ->orderBy('slot')
                            ->get();
    }
    
    /**
     * Obtener suplentes de un equipo para una gameweek
     */
    public function getSubstitutes(FantasyTeam $team, int $gameweekId): \Illuminate\Database\Eloquent\Collection
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $gameweekId)
                            ->where('is_starter', false)
                            ->with('player')
                            ->orderBy('slot')
                            ->get();
    }
    
    /**
     * Obtener capitán de un equipo para una gameweek
     */
    public function getCaptain(FantasyTeam $team, int $gameweekId): ?FantasyRoster
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $gameweekId)
                            ->where('captaincy', 1)
                            ->with('player')
                            ->first();
    }
    
    /**
     * Obtener vicecapitán de un equipo para una gameweek
     */
    public function getViceCaptain(FantasyTeam $team, int $gameweekId): ?FantasyRoster
    {
        return FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $gameweekId)
                            ->where('captaincy', 2)
                            ->with('player')
                            ->first();
    }
}