<?php

namespace App\Livewire\Manager;

use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use App\Models\Gameweek;
use Livewire\Component;
use Illuminate\Support\Collection;

class ManagerDashboard extends Component
{
    public Collection $leagueMembers;
    public ?int $selectedLeagueId = null;
    public ?LeagueMember $selectedMember = null;
    public ?FantasyTeam $selectedTeam = null;
    public ?Gameweek $currentGameweek = null;

    public function mount()
    {
        // Cargar todas las ligas del usuario con relaciones necesarias
        $this->leagueMembers = auth()->user()->leagueMembers()
            ->with([
                'league.season',
                'league.standings' => function($query) {
                    $query->orderBy('position')->limit(5);
                }
            ])
            ->where('is_active', true)
            ->get();

        // Seleccionar la primera liga por defecto
        if ($this->leagueMembers->isNotEmpty()) {
            $this->selectedLeagueId = $this->leagueMembers->first()->league_id;
            $this->loadSelectedLeague();
        }
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->loadSelectedLeague();
    }

    protected function loadSelectedLeague()
    {
        $this->selectedMember = $this->leagueMembers
            ->where('league_id', $this->selectedLeagueId)
            ->first();

        if ($this->selectedMember) {
            // Cargar el equipo fantasy del usuario en esta liga
            // SIEMPRE obtener datos frescos de la BD
            $this->selectedTeam = FantasyTeam::where('league_id', $this->selectedLeagueId)
                ->where('user_id', auth()->id())
                ->first();

            // Cargar gameweek actual de la temporada
            $this->currentGameweek = Gameweek::where('season_id', $this->selectedMember->league->season_id)
                ->whereDate('starts_at', '<=', now())
                ->whereDate('ends_at', '>=', now())
                ->first();
        }
    }

    public function render()
    {
        // 🆕 REFRESCAR EQUIPO ANTES DE RENDERIZAR
        // Esto asegura que siempre tengamos datos actualizados
        if ($this->selectedTeam) {
            $this->selectedTeam = $this->selectedTeam->fresh();
        }

        // 🆕 REFRESCAR MEMBER PARA VERIFICAR DEADLINE
        if ($this->selectedMember) {
            $this->selectedMember = $this->selectedMember->fresh();
        }

        return view('livewire.manager.manager-dashboard', [
            'hasIncompleteSquad' => $this->selectedTeam && !$this->selectedTeam->is_squad_complete,
            'hasDeadline' => $this->selectedMember && $this->selectedMember->squad_deadline_at,
            'standings' => $this->selectedMember 
                ? $this->selectedMember->league->standings()->orderBy('position')->limit(5)->get()
                : collect(),
        ]);
    }
}