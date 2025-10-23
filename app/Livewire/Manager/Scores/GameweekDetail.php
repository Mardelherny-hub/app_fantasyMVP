<?php

namespace App\Livewire\Manager\Scores;

use App\Models\Gameweek;
use App\Models\FantasyTeam;
use App\Models\FantasyRosterScore;
use Illuminate\Support\Collection;
use Livewire\Component;

class GameweekDetail extends Component
{
    // Props
    public int $gameweekId;
    public int $teamId;
    
    // Datos
    public Gameweek $gameweek;
    public FantasyTeam $team;
    public Collection $starters;
    public Collection $bench;
    
    // Estadísticas
    public int $totalPoints = 0;
    public int $startersPoints = 0;
    public ?FantasyRosterScore $captain = null;
    public ?FantasyRosterScore $viceCaptain = null;
    
    // UI State
    public ?int $selectedPlayerId = null;
    public bool $showBreakdownModal = false;
    public ?FantasyRosterScore $selectedPlayerScore = null;

    /**
     * Mount component
     */
    public function mount(int $gameweekId, int $teamId)
    {
        $this->gameweekId = $gameweekId;
        $this->teamId = $teamId;
        
        // Cargar gameweek y equipo
        $this->gameweek = Gameweek::with('season')->findOrFail($gameweekId);
        $this->team = FantasyTeam::with('league')->findOrFail($teamId);
        
        // Cargar scores
        $this->loadScores();
    }

    /**
     * Cargar scores del gameweek
     */
    protected function loadScores(): void
    {
        $scores = FantasyRosterScore::where('gameweek_id', $this->gameweekId)
            ->where('fantasy_team_id', $this->teamId)
            ->with(['player', 'fantasyRoster'])
            ->get();
        
        // Separar titulares y suplentes
        $this->starters = $scores->where('is_starter', true)->sortBy('fantasyRoster.slot');
        $this->bench = $scores->where('is_starter', false)->sortBy('fantasyRoster.slot');
        
        // Identificar capitanes
        $this->captain = $scores->where('is_captain', true)->first();
        $this->viceCaptain = $scores->where('is_vice_captain', true)->first();
        
        // Calcular puntos
        $this->startersPoints = $this->starters->sum('final_points');
        $this->totalPoints = $this->startersPoints;
    }

    /**
     * Mostrar breakdown de un jugador
     */
    public function showBreakdown(int $playerId): void
    {
        $this->selectedPlayerId = $playerId;
        
        $this->selectedPlayerScore = FantasyRosterScore::where('gameweek_id', $this->gameweekId)
            ->where('fantasy_team_id', $this->teamId)
            ->where('player_id', $playerId)
            ->with('player')
            ->first();
        
        $this->showBreakdownModal = true;
    }

    /**
     * Cerrar modal de breakdown
     */
    public function closeBreakdown(): void
    {
        $this->showBreakdownModal = false;
        $this->selectedPlayerId = null;
        $this->selectedPlayerScore = null;
    }

    /**
     * Obtener posición del jugador
     */
    public function getPositionLabel(int $position): string
    {
        return match($position) {
            1 => 'GK',
            2 => 'DF',
            3 => 'MF',
            4 => 'FW',
            default => '??'
        };
    }

    /**
     * Formatear breakdown para mostrar
     */
    public function getFormattedBreakdown(?array $breakdown): array
    {
        if (!$breakdown) {
            return [];
        }

        $formatted = [];
        
        foreach ($breakdown as $key => $item) {
            // Saltar items vacíos
            if (!isset($item['points']) || $item['points'] == 0) {
                continue;
            }
            
            $formatted[] = [
                'code' => $key,
                'label' => $item['label'] ?? $key,
                'points' => $item['points'],
            ];
        }
        
        return $formatted;
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.scores.gameweek-detail');
    }
}