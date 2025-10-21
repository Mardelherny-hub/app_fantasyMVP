<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Listing;
use App\Models\FantasyTeam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any listings.
     * Cualquier manager puede ver listings del mercado.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the listing.
     * Cualquier manager puede ver un listing específico.
     */
    public function view(User $user, Listing $listing): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can create listings.
     * Solo el dueño del equipo puede listar un jugador.
     */
    public function create(User $user, FantasyTeam $fantasyTeam): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // El equipo debe pertenecer al usuario
        // No permitir listar si es un equipo bot
        return $user->id === $fantasyTeam->user_id && !$fantasyTeam->is_bot;
    }

    /**
     * Determine if the user can update the listing.
     * Solo el vendedor puede actualizar el precio de su listing.
     */
    public function update(User $user, Listing $listing): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // Solo puede actualizar si es el dueño del equipo vendedor
        $sellerTeam = $listing->fantasyTeam;
        
        return $user->id === $sellerTeam->user_id && !$sellerTeam->is_bot;
    }

    /**
     * Determine if the user can delete/withdraw the listing.
     * Solo el vendedor puede retirar el listing (si no hay ofertas aceptadas).
     */
    public function delete(User $user, Listing $listing): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // Solo puede retirar si es el dueño del equipo vendedor
        $sellerTeam = $listing->fantasyTeam;
        
        if ($user->id !== $sellerTeam->user_id || $sellerTeam->is_bot) {
            return false;
        }

        // No puede retirar si hay ofertas aceptadas
        return $listing->canBeWithdrawn();
    }

    /**
     * Alias para withdraw - mismo comportamiento que delete.
     * Solo el vendedor puede retirar el listing (si no hay ofertas aceptadas).
     */
    public function withdraw(User $user, Listing $listing): bool
    {
        return $this->delete($user, $listing);
    }
}