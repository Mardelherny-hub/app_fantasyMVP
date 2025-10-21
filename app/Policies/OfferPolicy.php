<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Offer;
use App\Models\Listing;
use App\Models\FantasyTeam;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any offers.
     * Manager puede ver sus ofertas enviadas y recibidas.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the offer.
     * Manager puede ver oferta si es comprador o vendedor del listing.
     */
    public function view(User $user, Offer $offer): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        $buyerTeam = $offer->buyerTeam;
        $sellerTeam = $offer->listing->fantasyTeam;

        // Puede ver si es el comprador o el vendedor
        return $user->id === $buyerTeam->user_id || $user->id === $sellerTeam->user_id;
    }

    /**
     * Determine if the user can create offers.
     * No puede ofertar su propio listing + validaciones de presupuesto se hacen en el servicio.
     */
    public function create(User $user, Listing $listing, FantasyTeam $buyerTeam): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // El equipo comprador debe pertenecer al usuario
        if ($user->id !== $buyerTeam->user_id || $buyerTeam->is_bot) {
            return false;
        }

        // No puede ofertar por su propio listing
        $sellerTeam = $listing->fantasyTeam;
        if ($buyerTeam->id === $sellerTeam->id) {
            return false;
        }

        // El listing debe estar activo
        return $listing->isActive();
    }

    /**
     * Determine if the user can update the offer.
     * No aplica - las ofertas no se editan, solo se cancelan y crean nuevas.
     */
    public function update(User $user, Offer $offer): bool
    {
        return false;
    }

    /**
     * Determine if the user can delete/cancel the offer.
     * Solo el comprador puede cancelar oferta pendiente.
     */
    public function delete(User $user, Offer $offer): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        $buyerTeam = $offer->buyerTeam;

        // Solo puede cancelar si es el comprador
        if ($user->id !== $buyerTeam->user_id || $buyerTeam->is_bot) {
            return false;
        }

        // Solo puede cancelar si está pendiente
        return $offer->isPending();
    }

    /**
     * Alias para cancel - mismo comportamiento que delete.
     */
    public function cancel(User $user, Offer $offer): bool
    {
        return $this->delete($user, $offer);
    }

    /**
     * Determine if the user can accept the offer.
     * Solo el vendedor del listing puede aceptar.
     */
    public function accept(User $user, Offer $offer): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        $sellerTeam = $offer->listing->fantasyTeam;

        // Solo puede aceptar si es el vendedor
        if ($user->id !== $sellerTeam->user_id || $sellerTeam->is_bot) {
            return false;
        }

        // Solo puede aceptar si la oferta está pendiente
        if (!$offer->isPending()) {
            return false;
        }

        // El listing debe estar activo
        return $offer->listing->isActive();
    }

    /**
     * Determine if the user can reject the offer.
     * Solo el vendedor del listing puede rechazar.
     */
    public function reject(User $user, Offer $offer): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        $sellerTeam = $offer->listing->fantasyTeam;

        // Solo puede rechazar si es el vendedor
        if ($user->id !== $sellerTeam->user_id || $sellerTeam->is_bot) {
            return false;
        }

        // Solo puede rechazar si está pendiente
        return $offer->isPending();
    }
}