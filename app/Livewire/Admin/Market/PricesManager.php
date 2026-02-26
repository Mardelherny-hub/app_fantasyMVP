<?php

namespace App\Livewire\Admin\Market;

use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\Season;
use App\Services\Admin\Market\PriceManagementService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PricesManager extends Component
{
    use WithPagination;

    public Season $season;
    public array $stats = [];

    // Filtros
    public string $search = '';
    public ?int $filterPosition = null;
    public ?float $filterMinPrice = null;
    public ?float $filterMaxPrice = null;
    public string $sortBy = 'market_value';
    public string $sortDirection = 'desc';

    // Edición individual
    public ?int $editingPlayerId = null;
    public float $editPrice = 0;

    // Ajuste masivo
    public bool $showBulkModal = false;
    public string $bulkType = 'position'; // 'position' o 'range'
    public ?int $bulkPosition = null;
    public float $bulkPercentage = 0;
    public float $bulkMinPrice = 0;
    public float $bulkMaxPrice = 999999;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterPosition' => ['except' => null],
        'sortBy' => ['except' => 'market_value'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount(Season $season): void
    {
        $this->season = $season;
        $this->loadStats();
    }

    public function render(): View
    {
        $players = $this->getPlayers();

        return view('livewire.admin.market.prices-manager', [
            'players' => $players,
            'positions' => $this->getPositions(),
        ]);
    }

    /**
     * Obtener jugadores con filtros y paginación
     */
    private function getPlayers()
    {
        $query = Player::where('is_active', true)
            ->with(['valuations' => fn($q) => $q->where('season_id', $this->season->id)]);

        // Filtro de búsqueda
        if ($this->search) {
            $query->where('full_name', 'like', '%' . $this->search . '%');
        }

        // Filtro de posición
        if ($this->filterPosition) {
            $query->where('position', $this->filterPosition);
        }

        // Filtro de precio
        if ($this->filterMinPrice || $this->filterMaxPrice) {
            $query->whereHas('valuations', function ($q) {
                $q->where('season_id', $this->season->id);
                
                if ($this->filterMinPrice) {
                    $q->where('market_value', '>=', $this->filterMinPrice);
                }
                
                if ($this->filterMaxPrice) {
                    $q->where('market_value', '<=', $this->filterMaxPrice);
                }
            });
        }

        // Ordenamiento
        if ($this->sortBy === 'market_value') {
            $query->orderBy(
                PlayerValuation::select('market_value')
                    ->whereColumn('player_valuations.player_id', 'players.id')
                    ->where('season_id', $this->season->id)
                    ->limit(1),
                $this->sortDirection
            );
        } elseif ($this->sortBy === 'full_name') {
            $query->orderBy('full_name', $this->sortDirection);
        } elseif ($this->sortBy === 'position') {
            $query->orderBy('position', $this->sortDirection);
        }

        return $query->paginate(20);
    }

    /**
     * Cargar estadísticas
     */
    public function loadStats(): void
    {
        $service = app(PriceManagementService::class);
        $this->stats = $service->getPriceStats($this->season);
    }

    /**
     * Cambiar orden
     */
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Limpiar filtros
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'filterPosition', 'filterMinPrice', 'filterMaxPrice']);
        $this->resetPage();
    }

    /**
     * Iniciar edición de precio
     */
    public function startEdit(int $playerId): void
    {
        $player = Player::findOrFail($playerId);
        $this->editingPlayerId = $playerId;
        $value = $player->marketValue($this->season->id);
        $this->editPrice = $value !== null ? (float) $value : 0.50;
    }

    /**
     * Cancelar edición
     */
    public function cancelEdit(): void
    {
        $this->reset(['editingPlayerId', 'editPrice']);
    }

    /**
     * Guardar precio editado
     */
    public function savePrice(): void
    {
        $this->validate([
            'editPrice' => 'required|numeric|min:0.50|max:999999.99',
        ]);

        try {
            $player = Player::findOrFail($this->editingPlayerId);
            $service = app(PriceManagementService::class);

            $service->updatePlayerPrice($player, $this->season, $this->editPrice);

            $this->dispatch('notify', [
                'message' => __('Precio actualizado correctamente'),
                'type' => 'success'
            ]);

            $this->cancelEdit();
            $this->loadStats();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Abrir modal de ajuste masivo
     */
    public function openBulkModal(): void
    {
        $this->showBulkModal = true;
    }

    /**
     * Cerrar modal de ajuste masivo
     */
    public function closeBulkModal(): void
    {
        $this->showBulkModal = false;
        $this->reset(['bulkType', 'bulkPosition', 'bulkPercentage', 'bulkMinPrice', 'bulkMaxPrice']);
    }

    /**
     * Ejecutar ajuste masivo
     */
    public function executeBulkAdjustment(): void
    {
        $this->validate([
            'bulkPercentage' => 'required|numeric|min:-50|max:100',
        ]);

        try {
            $service = app(PriceManagementService::class);
            $result = null;

            if ($this->bulkType === 'position') {
                $this->validate([
                    'bulkPosition' => 'required|integer|in:1,2,3,4',
                ]);

                $result = $service->bulkAdjustByPosition(
                    $this->season,
                    $this->bulkPosition,
                    $this->bulkPercentage
                );

                $message = __(':count jugadores actualizados', ['count' => $result['updated']]);

            } else {
                $this->validate([
                    'bulkMinPrice' => 'required|numeric|min:0.50',
                    'bulkMaxPrice' => 'required|numeric|gt:bulkMinPrice',
                ]);

                $result = $service->bulkAdjustByPriceRange(
                    $this->season,
                    $this->bulkMinPrice,
                    $this->bulkMaxPrice,
                    $this->bulkPercentage
                );

                $message = __(':count jugadores actualizados', ['count' => $result['updated']]);
            }

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

            $this->closeBulkModal();
            $this->loadStats();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Actualizar temporada
     */
    public function updatedSeason($value): void
    {
        $this->season = Season::findOrFail($value);
        $this->loadStats();
        $this->resetPage();
    }

    /**
     * Resetear página al cambiar filtros
     */
    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'filterPosition', 'filterMinPrice', 'filterMaxPrice'])) {
            $this->resetPage();
        }
    }

    /**
     * Obtener posiciones
     */
    private function getPositions(): array
    {
        return [
            Player::POSITION_GK => 'GK',
            Player::POSITION_DF => 'DF',
            Player::POSITION_MF => 'MF',
            Player::POSITION_FW => 'FW',
        ];
    }
}