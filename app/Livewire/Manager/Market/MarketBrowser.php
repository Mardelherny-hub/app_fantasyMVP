<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Services\Manager\Market\MarketService;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class MarketBrowser extends Component
{
    // Equipo del manager
    public FantasyTeam $team;
    
    // Tab activo
    public string $activeTab = 'free-agents';
    
    // Estado del mercado
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    public int $transfersUsed = 0;
    public int $transfersLimit = 3;
    
    // Datos globales
    public float $availableBudget = 0;
    public array $squadLimits = [];
    
    // UI State
    public bool $loading = false;
    public ?string $successMessage = null;
    public ?string $errorMessage = null;
    
    // Tabs disponibles
    public array $tabs = [
        'free-agents' => 'Free Agents',
        'market-listings' => 'Players for Sale',
        'create-listing' => 'Sell Players',
        'my-listings' => 'My Listings',
        'offers' => 'Offers',
        'history' => 'Transfer History',
    ];
    
    // Listeners
    protected $listeners = [
        'refreshMarket' => '$refresh',
        'playerPurchased' => 'handlePlayerPurchased',
        'listingCreated' => 'handleListingCreated',
        'offerSent' => 'handleOfferSent',
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
            ->with('league.season', 'league.marketSettings')
            ->firstOrFail();

        // Verificar plantilla completa
        if (!$this->team->is_squad_complete) {
            session()->flash('error', __('Debes completar tu plantilla antes de acceder al mercado.'));
            return redirect()->route('manager.squad-builder.index', ['locale' => app()->getLocale()]);
        }

        // Cargar estado del mercado
        $this->loadMarketState();
    }

    /**
     * Cargar estado del mercado
     */
    public function loadMarketState(): void
    {
        $marketService = app(MarketService::class);
        
        // Gameweek actual
        $this->currentGameweek = Gameweek::where('season_id', $this->team->league->season_id)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        // Estado del mercado
        $this->marketOpen = $this->currentGameweek 
            ? $this->currentGameweek->starts_at->isFuture()
            : false;

        // Transferencias usadas
        if ($this->currentGameweek) {
            $cacheKey = "transfers_count_{$this->team->id}_{$this->currentGameweek->id}";
            $this->transfersUsed = Cache::get($cacheKey, 0);
        }

        // Presupuesto disponible
        $this->availableBudget = $this->team->budget;

        // Límites de plantilla
        $this->squadLimits = [
            'total' => 23,
            'GK' => ['min' => 1, 'max' => 3],
            'DF' => ['min' => 3, 'max' => 8],
            'MF' => ['min' => 3, 'max' => 8],
            'FW' => ['min' => 1, 'max' => 4],
        ];
    }

    /**
     * Cambiar de tab
     */
    public function switchTab(string $tab): void
    {
        if (!array_key_exists($tab, $this->tabs)) {
            return;
        }

        $this->activeTab = $tab;
        $this->clearMessages();
        $this->dispatch('tabChanged', tab: $tab);
    }

    /**
     * Refrescar mercado
     */
    public function refreshMarket(): void
    {
        $this->loadMarketState();
        $this->team = $this->team->fresh();
        $this->clearMessages();
        $this->dispatch('marketRefreshed');
    }

    /**
     * Handlers de eventos
     */
    public function handlePlayerPurchased($data): void
    {
        $this->successMessage = $data['message'] ?? __('Jugador fichado correctamente.');
        $this->refreshMarket();
    }

    public function handleListingCreated($data): void
    {
        $this->successMessage = $data['message'] ?? __('Jugador listado para venta.');
        $this->activeTab = 'my-listings';
        $this->refreshMarket();
    }

    public function handleOfferSent($data): void
    {
        $this->successMessage = $data['message'] ?? __('Oferta enviada correctamente.');
        $this->activeTab = 'offers';
        $this->refreshMarket();
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
     * Verificar si puede transferir
     */
    public function canTransfer(): bool
    {
        return $this->marketOpen && $this->transfersUsed < $this->transfersLimit;
    }

    /**
     * Obtener props para componentes hijos
     */
    public function getChildProps(): array
    {
        return [
            'team' => $this->team,
            'gameweek' => $this->currentGameweek,
            'marketOpen' => $this->marketOpen,
        ];
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.market.market-browser', [
            'canTransfer' => $this->canTransfer(),
            'transfersRemaining' => max(0, $this->transfersLimit - $this->transfersUsed),
            'childProps' => $this->getChildProps(),
        ]);
    }
}