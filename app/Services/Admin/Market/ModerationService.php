<?php

namespace App\Services\Admin\Market;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\League;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ModerationService
{
    /**
     * Cancelar un listing activo
     *
     * @param Listing $listing
     * @param string $reason
     * @param User $admin
     * @return void
     * @throws ValidationException
     */
    public function cancelListing(Listing $listing, string $reason, User $admin): void
    {
        if ($listing->status !== Listing::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'listing' => __('Solo se pueden cancelar listings activos')
            ]);
        }

        DB::transaction(function () use ($listing, $reason, $admin) {
            // Marcar listing como withdrawn
            $listing->update(['status' => Listing::STATUS_WITHDRAWN]);

            // Rechazar todas las ofertas pendientes
            Offer::where('listing_id', $listing->id)
                ->where('status', Offer::STATUS_PENDING)
                ->update(['status' => Offer::STATUS_REJECTED]);

            // Registrar acción de moderación
            $this->logModerationAction(
                admin: $admin,
                action: 'cancel_listing',
                target_type: 'Listing',
                target_id: $listing->id,
                reason: $reason,
                metadata: [
                    'player_id' => $listing->player_id,
                    'seller_team_id' => $listing->fantasy_team_id,
                    'price' => $listing->price,
                ]
            );

            Log::warning('Listing cancelled by admin', [
                'listing_id' => $listing->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);
        });
    }

    /**
     * Revertir una transferencia
     *
     * @param Transfer $transfer
     * @param string $reason
     * @param User $admin
     * @return void
     * @throws ValidationException
     */
    public function revertTransfer(Transfer $transfer, string $reason, User $admin): void
    {
        if (!$transfer->isEffective()) {
            throw ValidationException::withMessages([
                'transfer' => __('La transferencia aún no ha sido aplicada')
            ]);
        }

        DB::transaction(function () use ($transfer, $reason, $admin) {
            // Obtener equipos involucrados
            $buyerTeam = $transfer->toTeam;
            $sellerTeam = $transfer->fromTeam;

            // Revertir presupuestos
            if ($buyerTeam) {
                $buyerTeam->updateBudget($transfer->price); // Devolver dinero al comprador
            }

            if ($sellerTeam) {
                $sellerTeam->updateBudget(-$transfer->price); // Quitar dinero al vendedor
            }

            // Nota: El roster NO se revierte automáticamente
            // ya que puede estar siendo usado en gameweeks actuales
            // Esto debe manejarse manualmente por el admin

            // Registrar acción de moderación
            $this->logModerationAction(
                admin: $admin,
                action: 'revert_transfer',
                target_type: 'Transfer',
                target_id: $transfer->id,
                reason: $reason,
                metadata: [
                    'player_id' => $transfer->player_id,
                    'from_team_id' => $transfer->from_fantasy_team_id,
                    'to_team_id' => $transfer->to_fantasy_team_id,
                    'price' => $transfer->price,
                    'type' => $transfer->type,
                ]
            );

            Log::critical('Transfer reverted by admin', [
                'transfer_id' => $transfer->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);
        });
    }

    /**
     * Bloquear usuario del mercado
     *
     * @param User $user
     * @param string $reason
     * @param int $hours 0 = permanente
     * @param User $admin
     * @return void
     */
    public function blockUserFromMarket(User $user, string $reason, int $hours, User $admin): void
    {
        $cacheKey = "market_blocked_user_{$user->id}";
        $expiresAt = $hours > 0 ? now()->addHours($hours) : null;

        if ($expiresAt) {
            Cache::put($cacheKey, [
                'reason' => $reason,
                'blocked_by' => $admin->id,
                'blocked_at' => now(),
                'expires_at' => $expiresAt,
            ], $expiresAt);
        } else {
            // Bloqueo permanente
            Cache::forever($cacheKey, [
                'reason' => $reason,
                'blocked_by' => $admin->id,
                'blocked_at' => now(),
                'expires_at' => null,
            ]);
        }

        // Cancelar todos los listings activos del usuario
        $this->cancelUserActiveListings($user, $reason, $admin);

        // Registrar acción
        $this->logModerationAction(
            admin: $admin,
            action: 'block_user',
            target_type: 'User',
            target_id: $user->id,
            reason: $reason,
            metadata: [
                'hours' => $hours,
                'permanent' => $hours === 0,
            ]
        );

        Log::warning('User blocked from market', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'hours' => $hours,
            'reason' => $reason,
        ]);
    }

    /**
     * Desbloquear usuario del mercado
     *
     * @param User $user
     * @param User $admin
     * @return void
     */
    public function unblockUser(User $user, User $admin): void
    {
        $cacheKey = "market_blocked_user_{$user->id}";

        Cache::forget($cacheKey);

        $this->logModerationAction(
            admin: $admin,
            action: 'unblock_user',
            target_type: 'User',
            target_id: $user->id,
            reason: 'Unblocked by admin',
            metadata: []
        );

        Log::info('User unblocked from market', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
        ]);
    }

    /**
     * Verificar si usuario está bloqueado
     *
     * @param User $user
     * @return array|null
     */
    public function isUserBlocked(User $user): ?array
    {
        $cacheKey = "market_blocked_user_{$user->id}";
        return Cache::get($cacheKey);
    }

    /**
     * Obtener actividad sospechosa
     *
     * @param League|null $league
     * @return array
     */
    public function getSuspiciousActivity(?League $league = null): array
    {
        $activity = [];

        // 1. Transfers con precios anormales
        $activity['unusual_prices'] = $this->getUnusualPriceTransfers($league);

        // 2. Usuarios muy activos
        $activity['hyperactive_users'] = $this->getHyperactiveUsers($league);

        // 3. Ofertas rechazadas repetidamente
        $activity['repeated_rejections'] = $this->getRepeatedRejections($league);

        return $activity;
    }

    /**
     * Cancelar todos los listings activos de un usuario
     */
    private function cancelUserActiveListings(User $user, string $reason, User $admin): void
    {
        $listings = Listing::where('status', Listing::STATUS_ACTIVE)
            ->whereHas('fantasyTeam', fn($q) => $q->where('user_id', $user->id))
            ->get();

        foreach ($listings as $listing) {
            $this->cancelListing($listing, $reason, $admin);
        }
    }

    /**
     * Obtener transfers con precios anormales
     */
    private function getUnusualPriceTransfers(?League $league): array
    {
        $query = Transfer::with(['player', 'toTeam', 'fromTeam'])
            ->where('created_at', '>=', now()->subDays(7));

        if ($league) {
            $query->where('league_id', $league->id);
        }

        $transfers = $query->get();
        $unusual = [];

        foreach ($transfers as $transfer) {
            $marketValue = $transfer->player->marketValue($transfer->league->season_id);

            if (!$marketValue) continue;

            $ratio = $transfer->price / $marketValue;

            // Detectar precios muy altos (>150%) o muy bajos (<50%)
            if ($ratio > 1.5 || $ratio < 0.5) {
                $unusual[] = [
                    'transfer' => $transfer,
                    'market_value' => $marketValue,
                    'ratio' => $ratio,
                    'severity' => $ratio > 2 || $ratio < 0.3 ? 'high' : 'medium',
                ];
            }
        }

        return $unusual;
    }

    /**
     * Obtener usuarios hiperactivos
     */
    private function getHyperactiveUsers(?League $league): array
    {
        $query = Transfer::where('created_at', '>=', now()->subDays(1))
            ->selectRaw('to_fantasy_team_id as team_id, COUNT(*) as count')
            ->groupBy('to_fantasy_team_id')
            ->having('count', '>', 10);

        if ($league) {
            $query->where('league_id', $league->id);
        }

        return $query->with('toTeam.user')->get()->toArray();
    }

    /**
     * Obtener ofertas rechazadas repetidamente
     */
    private function getRepeatedRejections(?League $league): array
    {
        $query = Offer::where('status', Offer::STATUS_REJECTED)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('buyer_fantasy_team_id as team_id, COUNT(*) as count')
            ->groupBy('buyer_fantasy_team_id')
            ->having('count', '>', 15);

        return $query->with('buyerTeam.user')->get()->toArray();
    }

    /**
     * Registrar acción de moderación
     */
    private function logModerationAction(
        User $admin,
        string $action,
        string $target_type,
        int $target_id,
        string $reason,
        array $metadata
    ): void {
        Log::channel('moderation')->info('Moderation action', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'action' => $action,
            'target_type' => $target_type,
            'target_id' => $target_id,
            'reason' => $reason,
            'metadata' => $metadata,
            'timestamp' => now(),
        ]);
    }
}