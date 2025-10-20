<?php

namespace App\Livewire\Manager\Lineup;

use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\FantasyRoster;
use App\Services\Manager\LineupService;
use App\Services\Manager\FormationService;
use Livewire\Component;
use Illuminate\Support\Collection;

class LineupManager extends Component
{
    // Propiedades del equipo y gameweek
    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public ?Gameweek $selectedGameweek = null;
    public Collection $availableGameweeks;
    
    // Datos de la alineación
    public Collection $starters;
    public Collection $bench;
    public ?FantasyRoster $captain = null;
    public ?FantasyRoster $viceCaptain = null;
    
    // Estado de formación
    public ?string $currentFormation = null;
    public array $formationStats = [];
    public bool $isValidLineup = false;
    
    // UI State
    public bool $canEdit = false;
    public bool $loading = false;
    public bool $showPlayerModal = false;
    public ?int $selectedPlayerId = null;
    public ?FantasyRoster $selectedPlayerRoster = null;
    
    // Mensajes
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    // Listeners para eventos
    protected $listeners = [
        'refreshLineup' => '$refresh',
        'playerSelected' => 'handlePlayerSelected',
    ];

    /**
     * Inicializar componente
     */
    public function mount()
    {
        $user = auth()->user();
        
        // Obtener equipo del manager
        $this->team = FantasyTeam::where('user_id', $user->id)
            ->whereNotNull('league_id')
            ->with('league.season')
            ->firstOrFail();
        
        // 🆕 REFRESCAR para obtener valor actual de is_squad_complete
        $this->team->refresh();

        // Verificar que completó su plantilla
        if (!$this->team->is_squad_complete) {
            session()->flash('error', __('Primero debes completar tu plantilla.'));
            return redirect()->route('manager.squad-builder.index', ['locale' => app()->getLocale()]);
        }

        // Cargar gameweeks disponibles
        $this->loadAvailableGameweeks();

        // Seleccionar gameweek actual por defecto
        $this->currentGameweek = Gameweek::where('season_id', $this->team->league->season_id)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        $this->selectedGameweek = $this->currentGameweek ?? $this->availableGameweeks->first();

        // Cargar alineación
        $this->loadLineup();
    }

    /**
     * Cargar gameweeks disponibles
     */
    protected function loadAvailableGameweeks(): void
    {
        $this->availableGameweeks = Gameweek::where('season_id', $this->team->league->season_id)
            ->orderBy('number')
            ->get();
    }

    /**
     * Cargar alineación actual
     */
    protected function loadLineup(): void
    {
        if (!$this->selectedGameweek) {
            return;
        }

        // 1. RESETEAR propiedades primero (evita referencias residuales)
        $this->starters = collect();
        $this->bench = collect();
        $this->captain = null;
        $this->viceCaptain = null;

        // 2. Consulta FRESCA desde BD
        $roster = FantasyRoster::where('fantasy_team_id', $this->team->id)
            ->where('gameweek_id', $this->selectedGameweek->id)
            ->with('player')
            ->orderBy('slot')
            ->get();

        // 3. Asignar colecciones nuevas
        $this->starters = $roster->where('is_starter', true)->values();
        $this->bench = $roster->where('is_starter', false)->values();
        $this->captain = $roster->firstWhere('captaincy', FantasyRoster::CAPTAINCY_CAPTAIN);
        $this->viceCaptain = $roster->firstWhere('captaincy', FantasyRoster::CAPTAINCY_VICE);

        // Verificar si se puede editar
        $lineupService = app(LineupService::class);
        $this->canEdit = $lineupService->canEditLineup($this->selectedGameweek);

        // Detectar formación
        $formationService = app(FormationService::class);
        $this->currentFormation = $formationService->detectFormation($this->starters);
        
        // Obtener estadísticas
        $this->formationStats = $lineupService->getLineupStats($this->team, $this->selectedGameweek->id);
        $this->isValidLineup = $this->formationStats['is_valid'];
    }

    /**
     * Cambiar gameweek seleccionada
     */
    public function selectGameweek(int $gameweekId): void
    {
        $this->selectedGameweek = $this->availableGameweeks->firstWhere('id', $gameweekId);
        $this->loadLineup();
        $this->clearMessages();
    }

    /**
     * Navegar a gameweek anterior
     */
    public function previousGameweek(): void
    {
        if (!$this->selectedGameweek) {
            return;
        }

        $previous = $this->availableGameweeks
            ->where('number', '<', $this->selectedGameweek->number)
            ->sortByDesc('number')
            ->first();

        if ($previous) {
            $this->selectGameweek($previous->id);
        }
    }

    /**
     * Navegar a gameweek siguiente
     */
    public function nextGameweek(): void
    {
        if (!$this->selectedGameweek) {
            return;
        }

        $next = $this->availableGameweeks
            ->where('number', '>', $this->selectedGameweek->number)
            ->sortBy('number')
            ->first();

        if ($next) {
            $this->selectGameweek($next->id);
        }
    }

    /**
     * Intercambiar dos jugadores
     */
    public function swapPlayers(int $player1Id, int $player2Id): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->swapPlayers(
                $this->team,
                $this->selectedGameweek->id,
                $player1Id,
                $player2Id
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Mover jugador a titulares
     */
    public function moveToStarting(int $playerId, int $targetSlot): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->moveToStarting(
                $this->team,
                $this->selectedGameweek->id,
                $playerId,
                $targetSlot
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Mover jugador al banco
     */
    public function moveToBench(int $playerId, int $targetSlot): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->moveToBench(
                $this->team,
                $this->selectedGameweek->id,
                $playerId,
                $targetSlot
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Agregar jugador a titulares (método simplificado)
     */
    public function addToStarting(int $playerId): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        // Cerrar modal primero
        $this->closePlayerModal();

        try {
            $this->loading = true;
            
            // Encontrar primer slot disponible en titulares
            $targetSlot = $this->findAvailableStarterSlot();
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->moveToStarting(
                $this->team,
                $this->selectedGameweek->id,
                $playerId,
                $targetSlot
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Remover jugador de titulares (método simplificado)
     */
    public function removeFromStarting(int $playerId): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        // Cerrar modal primero
        $this->closePlayerModal();

        try {
            $this->loading = true;
            
            // Encontrar primer slot disponible en banco
            $targetSlot = $this->findAvailableBenchSlot();
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->moveToBench(
                $this->team,
                $this->selectedGameweek->id,
                $playerId,
                $targetSlot
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Asignar capitán
     */
    public function setCaptain(int $playerId): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->assignCaptain(
                $this->team,
                $this->selectedGameweek->id,
                $playerId
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('captain-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Asignar vicecapitán
     */
    public function setViceCaptain(int $playerId): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;
            
            $lineupService = app(LineupService::class);
            $result = $lineupService->assignViceCaptain(
                $this->team,
                $this->selectedGameweek->id,
                $playerId
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('vice-captain-updated');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Abrir modal de jugador
     */
    public function openPlayerModal(int $playerId): void
    {
        $this->selectedPlayerId = $playerId;
        
        // Consultar directamente desde BD en lugar de usar las colecciones
        $this->selectedPlayerRoster = FantasyRoster::where('fantasy_team_id', $this->team->id)
            ->where('gameweek_id', $this->selectedGameweek->id)
            ->where('player_id', $playerId)
            ->with('player')
            ->first();
        
        $this->showPlayerModal = true;
    }

    /**
     * Cerrar modal de jugador
     */
    public function closePlayerModal(): void
    {
        $this->showPlayerModal = false;
        $this->selectedPlayerId = null;
        $this->selectedPlayerRoster = null;
    }

    /**
     * Manejar selección de jugador desde modal
     */
    public function handlePlayerSelected(int $playerId, string $action): void
    {
        $this->closePlayerModal();

        switch ($action) {
            case 'set_captain':
                $this->setCaptain($playerId);
                break;
            case 'set_vice':
                $this->setViceCaptain($playerId);
                break;
            case 'to_starting':
                // Encontrar primer slot disponible en titulares
                $slot = $this->findAvailableStarterSlot();
                $this->moveToStarting($playerId, $slot);
                break;
            case 'to_bench':
                // Encontrar primer slot disponible en banco
                $slot = $this->findAvailableBenchSlot();
                $this->moveToBench($playerId, $slot);
                break;
        }
    }

    /**
     * Guardar todos los cambios
     */
    public function saveLineup(): void
    {
        if (!$this->canEdit) {
            $this->errorMessage = __('No puedes editar esta alineación.');
            return;
        }

        try {
            $this->loading = true;

            // Preparar datos
            $lineupData = [];

            foreach ($this->starters as $roster) {
                $lineupData[] = [
                    'player_id' => $roster->player_id,
                    'slot' => $roster->slot,
                    'is_starter' => true,
                    'captaincy' => $roster->captaincy,
                ];
            }

            foreach ($this->bench as $roster) {
                $lineupData[] = [
                    'player_id' => $roster->player_id,
                    'slot' => $roster->slot,
                    'is_starter' => false,
                    'captaincy' => $roster->captaincy,
                ];
            }

            $lineupService = app(LineupService::class);
            $result = $lineupService->saveLineup(
                $this->team,
                $this->selectedGameweek->id,
                $lineupData
            );

            $this->successMessage = $result['message'];
            $this->loadLineup();
            $this->dispatch('lineup-saved');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Encontrar slot disponible en titulares
     */
    protected function findAvailableStarterSlot(): int
    {
        $usedSlots = $this->starters->pluck('slot')->toArray();
        
        for ($slot = 1; $slot <= 11; $slot++) {
            if (!in_array($slot, $usedSlots)) {
                return $slot;
            }
        }
        
        return 1; // Fallback
    }

    /**
     * Encontrar slot disponible en banco
     */
    protected function findAvailableBenchSlot(): int
    {
        $usedSlots = $this->bench->pluck('slot')->toArray();
        
        for ($slot = 12; $slot <= 23; $slot++) {
            if (!in_array($slot, $usedSlots)) {
                return $slot;
            }
        }
        
        return 12; // Fallback
    }

    /**
     * Limpiar mensajes
     */
    protected function clearMessages(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    /**
     * Refrescar alineación
     */
    public function refreshLineup(): void
    {
        $this->loadLineup();
        $this->clearMessages();
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        $formationService = app(FormationService::class);
        
        return view('livewire.manager.lineup.lineup-manager', [
            'availableFormations' => $formationService->getAvailableFormations(),
            'hasPreviousGW' => $this->selectedGameweek 
                ? $this->availableGameweeks->where('number', '<', $this->selectedGameweek->number)->isNotEmpty()
                : false,
            'hasNextGW' => $this->selectedGameweek
                ? $this->availableGameweeks->where('number', '>', $this->selectedGameweek->number)->isNotEmpty()
                : false,
        ]);
    }
}