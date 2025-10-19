<?php

namespace App\Livewire\Manager\SquadBuilder;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\SquadDraft;
use App\Services\Manager\SquadBuilderService;
use App\Services\Manager\SquadValidationService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class SquadBuilderWizard extends Component
{
    use WithPagination;

    // Propiedades del wizard
    public $currentStep = 1;
    public $team;
    public $draft;
    public $summary;
    public $deadline;
    
    // Filtros de jugadores
    public $search = '';
    public $positionFilter = null;
    public $sortBy = 'price'; // price, name
    public $sortDirection = 'asc';
    
    // Selección de capitanes
    public $captainId = null;
    public $viceCaptainId = null;
    
    // Estados
    public $loading = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'currentStep' => ['except' => 1],
    ];

    /**
     * Mount component
     */
    public function mount()
    {
        $user = auth()->user();
        
        // Obtener equipo del manager
        $this->team = FantasyTeam::where('user_id', $user->id)
                                  ->whereNotNull('league_id')
                                  ->with('league.season')
                                  ->first();
        
        if (!$this->team) {
            session()->flash('error', __('Primero debes unirte a una liga.'));
            return redirect()->route('manager.onboarding.welcome', ['locale' => app()->getLocale()]);
        }
        
        // Si ya completó el armado, redirigir
        if ($this->team->is_squad_complete) {
            session()->flash('info', __('Ya completaste tu plantilla.'));
            return redirect()->route('manager.dashboard', ['locale' => app()->getLocale()]);
        }
        
        // Verificar si la liga está bloqueada
        if ($this->team->league->is_locked) {
            session()->flash('error', __('La liga ya está cerrada.'));
            return redirect()->route('manager.dashboard', ['locale' => app()->getLocale()]);
        }
        
        // Obtener o crear borrador
        $squadBuilderService = app(SquadBuilderService::class);
        $this->draft = $squadBuilderService->getOrCreateDraft($this->team);
        
        // Cargar paso guardado
        $this->currentStep = $this->draft->current_step;
        
        // Obtener resumen
        $this->updateSummary();
        
        // Obtener deadline
        $member = $this->team->league->members()->where('user_id', $user->id)->first();
        $this->deadline = $member ? $member->squad_deadline_at : null;
    }

    /**
     * Actualizar resumen del draft
     */
    public function updateSummary()
    {
        $validationService = app(SquadValidationService::class);
        $this->summary = $validationService->getDraftSummary($this->draft);
    }

    /**
     * Agregar jugador a la plantilla
     */
    public function addPlayer($playerId)
    {
        try {
            $this->loading = true;
            
            $player = Player::findOrFail($playerId);
            $position = $this->getPositionForCurrentStep();
            
            $squadBuilderService = app(SquadBuilderService::class);
            $result = $squadBuilderService->addPlayer($this->team, $player, $position);
            
            // Refrescar draft y summary
            $this->draft = $result['draft'];
            $this->updateSummary();
            
            $this->dispatch('player-added', [
                'player' => $player->known_as ?? $player->full_name,
                'price' => $result['draft']->budget_spent,
            ]);
            
            session()->flash('success', __('Jugador agregado correctamente.'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstError = reset($errors);
            session()->flash('error', is_array($firstError) ? $firstError[0] : $firstError);
            
        } catch (\Exception $e) {
            Log::error('Error adding player: ' . $e->getMessage());
            session()->flash('error', __('Error al agregar jugador.'));
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Remover jugador de la plantilla
     */
    public function removePlayer($playerId)
    {
        try {
            $this->loading = true;
            
            $squadBuilderService = app(SquadBuilderService::class);
            $result = $squadBuilderService->removePlayer($this->team, $playerId);
            
            // Refrescar draft y summary
            $this->draft = $result['draft'];
            $this->updateSummary();
            
            $this->dispatch('player-removed');
            
            session()->flash('success', __('Jugador removido correctamente.'));
            
        } catch (\Exception $e) {
            Log::error('Error removing player: ' . $e->getMessage());
            session()->flash('error', __('Error al remover jugador.'));
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Cambiar de paso
     */
    public function goToStep($step)
    {
        if ($step < 1 || $step > 5) {
            return;
        }
        
        $this->currentStep = $step;
        
        // Guardar progreso
        $squadBuilderService = app(SquadBuilderService::class);
        $squadBuilderService->updateStep($this->team, $step);
        
        // Actualizar posición filter según el paso
        $this->positionFilter = $this->getPositionForCurrentStep();
        
        $this->resetPage();
    }

    /**
     * Completar armado
     */
    public function completeSquad()
    {
        $this->validate([
            'captainId' => 'required|exists:players,id',
            'viceCaptainId' => 'required|exists:players,id|different:captainId',
        ]);
        
        try {
            $this->loading = true;
            
            $captainData = [
                'captain_id' => $this->captainId,
                'vice_captain_id' => $this->viceCaptainId,
            ];
            
            $squadBuilderService = app(SquadBuilderService::class);
            $result = $squadBuilderService->completeSquad($this->team, $captainData);
            
            session()->flash('success', __('¡Plantilla completada! Tu equipo está listo para la GW1.'));
            
            return redirect()->route('manager.dashboard', ['locale' => app()->getLocale()]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstError = reset($errors);
            session()->flash('error', is_array($firstError) ? $firstError[0] : $firstError);
            
        } catch (\Exception $e) {
            Log::error('Error completing squad: ' . $e->getMessage());
            session()->flash('error', __('Error al completar plantilla.'));
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Obtener posición según el paso actual
     */
    protected function getPositionForCurrentStep()
    {
        return match($this->currentStep) {
            1 => Player::POSITION_GK, // Arqueros
            2 => Player::POSITION_DF, // Defensores
            3 => Player::POSITION_MF, // Mediocampistas
            4 => Player::POSITION_FW, // Delanteros
            default => null,
        };
    }

    /**
     * Obtener jugadores disponibles según el paso actual
     */
    public function getAvailablePlayers()
    {
        $position = $this->getPositionForCurrentStep();
        
        if (!$position) {
            return collect([]);
        }
        
        $seasonId = $this->team->league->season_id;
        $selectedIds = $this->draft->getSelectedPlayerIds();
        
        $query = Player::where('is_active', true)
                       ->where('position', $position)
                       ->with(['valuations' => function($q) use ($seasonId) {
                           $q->where('season_id', $seasonId);
                       }])
                       ->whereNotIn('id', $selectedIds);
        
        // Búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('known_as', 'like', '%' . $this->search . '%');
            });
        }
        
        // Ordenamiento
        if ($this->sortBy === 'price') {
            $query->leftJoin('player_valuations', function($join) use ($seasonId) {
                $join->on('players.id', '=', 'player_valuations.player_id')
                     ->where('player_valuations.season_id', '=', $seasonId);
            })->orderBy('player_valuations.market_value', $this->sortDirection);
        } else {
            $query->orderBy('known_as', $this->sortDirection);
        }
        
        return $query->paginate(12);
    }

    /**
     * Obtener jugadores seleccionados con detalles
     */
    public function getSelectedPlayersWithDetails()
    {
        $selectedPlayers = $this->draft->selected_players ?? [];
        $playerIds = collect($selectedPlayers)->pluck('player_id')->toArray();
        
        return Player::whereIn('id', $playerIds)
                     ->with(['valuations' => function($q) {
                         $q->where('season_id', $this->team->league->season_id);
                     }])
                     ->get()
                     ->map(function($player) use ($selectedPlayers) {
                         $playerData = collect($selectedPlayers)->firstWhere('player_id', $player->id);
                         $player->selected_position = $playerData['position'] ?? null;
                         $player->selected_price = $playerData['price'] ?? 0;
                         return $player;
                     });
    }

    /**
     * Render component
     */
    public function render()
    {
        $availablePlayers = $this->getAvailablePlayers();
        $selectedPlayers = $this->getSelectedPlayersWithDetails();
        
        return view('livewire.manager.squad-builder.squad-builder-wizard', [
            'availablePlayers' => $availablePlayers,
            'selectedPlayers' => $selectedPlayers,
        ]);
    }
}