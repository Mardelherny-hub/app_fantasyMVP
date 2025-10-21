<?php

namespace App\Livewire\Admin\Market;

use App\Models\Listing;
use App\Models\Transfer;
use App\Models\User;
use App\Models\League;
use App\Services\Admin\Market\ModerationService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ModerationPanel extends Component
{
    use WithPagination;

    public ?League $selectedLeague = null;
    public array $suspiciousActivity = [];

    // Modal cancelar listing
    public bool $showCancelListingModal = false;
    public ?Listing $targetListing = null;
    public string $cancelReason = '';

    // Modal revertir transfer
    public bool $showRevertTransferModal = false;
    public ?Transfer $targetTransfer = null;
    public string $revertReason = '';
    public bool $revertConfirmed = false;

    // Modal bloquear usuario
    public bool $showBlockUserModal = false;
    public ?User $targetUser = null;
    public string $blockReason = '';
    public int $blockHours = 24;

    // Filtros
    public string $activityTab = 'unusual_prices'; // unusual_prices, hyperactive_users, repeated_rejections

    protected $queryString = [
        'activityTab' => ['except' => 'unusual_prices'],
    ];

    public function mount(?League $selectedLeague = null): void
    {
        $this->selectedLeague = $selectedLeague;
        $this->loadSuspiciousActivity();
    }

    public function render(): View
    {
        return view('livewire.admin.market.moderation-panel');
    }

    /**
     * Cargar actividad sospechosa
     */
    public function loadSuspiciousActivity(): void
    {
        $service = app(ModerationService::class);
        $this->suspiciousActivity = $service->getSuspiciousActivity($this->selectedLeague);
    }

    /**
     * Cambiar tab de actividad
     */
    public function setActivityTab(string $tab): void
    {
        $this->activityTab = $tab;
    }

    /**
     * Abrir modal cancelar listing
     */
    public function openCancelListingModal(int $listingId): void
    {
        $this->targetListing = Listing::with(['player', 'fantasyTeam'])->findOrFail($listingId);
        $this->showCancelListingModal = true;
        $this->cancelReason = '';
    }

    /**
     * Cerrar modal cancelar listing
     */
    public function closeCancelListingModal(): void
    {
        $this->showCancelListingModal = false;
        $this->reset(['targetListing', 'cancelReason']);
    }

    /**
     * Ejecutar cancelación de listing
     */
    public function executeCancelListing(): void
    {
        $this->validate([
            'cancelReason' => 'required|string|min:10|max:500',
        ]);

        try {
            $service = app(ModerationService::class);
            $service->cancelListing(
                $this->targetListing,
                $this->cancelReason,
                auth()->user()
            );

            $this->dispatch('notify', [
                'message' => __('Listing cancelado correctamente'),
                'type' => 'success'
            ]);

            $this->closeCancelListingModal();
            $this->loadSuspiciousActivity();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Abrir modal revertir transfer
     */
    public function openRevertTransferModal(int $transferId): void
    {
        $this->targetTransfer = Transfer::with(['player', 'toTeam', 'fromTeam'])->findOrFail($transferId);
        $this->showRevertTransferModal = true;
        $this->revertReason = '';
        $this->revertConfirmed = false;
    }

    /**
     * Cerrar modal revertir transfer
     */
    public function closeRevertTransferModal(): void
    {
        $this->showRevertTransferModal = false;
        $this->reset(['targetTransfer', 'revertReason', 'revertConfirmed']);
    }

    /**
     * Ejecutar reversión de transfer
     */
    public function executeRevertTransfer(): void
    {
        $this->validate([
            'revertReason' => 'required|string|min:10|max:500',
            'revertConfirmed' => 'accepted',
        ], [
            'revertConfirmed.accepted' => __('Debes confirmar que entiendes las consecuencias'),
        ]);

        try {
            $service = app(ModerationService::class);
            $service->revertTransfer(
                $this->targetTransfer,
                $this->revertReason,
                auth()->user()
            );

            $this->dispatch('notify', [
                'message' => __('Transfer revertido. IMPORTANTE: Debes ajustar el roster manualmente'),
                'type' => 'warning'
            ]);

            $this->closeRevertTransferModal();
            $this->loadSuspiciousActivity();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Abrir modal bloquear usuario
     */
    public function openBlockUserModal(int $userId): void
    {
        $this->targetUser = User::findOrFail($userId);
        $this->showBlockUserModal = true;
        $this->blockReason = '';
        $this->blockHours = 24;
    }

    /**
     * Cerrar modal bloquear usuario
     */
    public function closeBlockUserModal(): void
    {
        $this->showBlockUserModal = false;
        $this->reset(['targetUser', 'blockReason', 'blockHours']);
    }

    /**
     * Ejecutar bloqueo de usuario
     */
    public function executeBlockUser(): void
    {
        $this->validate([
            'blockReason' => 'required|string|min:10|max:500',
            'blockHours' => 'required|integer|min:0|max:8760', // Max 1 año
        ]);

        try {
            $service = app(ModerationService::class);
            $service->blockUserFromMarket(
                $this->targetUser,
                $this->blockReason,
                $this->blockHours,
                auth()->user()
            );

            $message = $this->blockHours > 0
                ? __('Usuario bloqueado por :hours horas', ['hours' => $this->blockHours])
                : __('Usuario bloqueado permanentemente');

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

            $this->closeBlockUserModal();
            $this->loadSuspiciousActivity();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Desbloquear usuario
     */
    public function unblockUser(int $userId): void
    {
        try {
            $user = User::findOrFail($userId);
            $service = app(ModerationService::class);

            $service->unblockUser($user, auth()->user());

            $this->dispatch('notify', [
                'message' => __('Usuario desbloqueado correctamente'),
                'type' => 'success'
            ]);

            $this->loadSuspiciousActivity();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Actualizar liga seleccionada
     */
    public function updatedSelectedLeague($value): void
    {
        $this->selectedLeague = $value ? League::find($value) : null;
        $this->loadSuspiciousActivity();
    }
}