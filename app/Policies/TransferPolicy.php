<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transfer;
use App\Models\FantasyTeam;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any transfers.
     * Manager puede ver su historial de transferencias.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('manager') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the transfer.
     * Manager puede ver transfer si participó como comprador o vendedor.
     */
    public function view(User $user, Transfer $transfer): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // Admin puede ver todos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Obtener equipos involucrados
        $buyerTeam = $transfer->toTeam;
        $sellerTeam = $transfer->fromTeam; // Puede ser null (agente libre)

        // Puede ver si es el comprador
        if ($buyerTeam && $user->id === $buyerTeam->user_id) {
            return true;
        }

        // Puede ver si es el vendedor (si existe - no es agente libre)
        if ($sellerTeam && $user->id === $sellerTeam->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create transfers.
     * Las transferencias NO se crean manualmente por usuarios.
     * Se crean automáticamente al aceptar ofertas o comprar agentes libres.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can update the transfer.
     * Las transferencias NO se pueden editar - son registros inmutables.
     */
    public function update(User $user, Transfer $transfer): bool
    {
        return false;
    }

    /**
     * Determine if the user can delete the transfer.
     * Las transferencias NO se pueden eliminar - son registros históricos.
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        return false;
    }

    /**
     * Determine if the user can view transfers for a specific team.
     * Útil para componentes que muestran historial de un equipo.
     */
    public function viewForTeam(User $user, FantasyTeam $team): bool
    {
        // Debe ser manager o admin
        if (!$user->hasRole('manager') && !$user->hasRole('admin')) {
            return false;
        }

        // Admin puede ver todos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Puede ver si es el dueño del equipo
        return $user->id === $team->user_id;
    }
}