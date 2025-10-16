<?php

namespace App\Livewire\Manager\Onboarding;

use App\Models\League;
use App\Models\LeagueMember;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;

class PublicLeaguesList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedLeagueId = null;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function joinLeague($leagueId)
    {
        $league = League::findOrFail($leagueId);
        $user = auth()->user();

        // Verificar límite de ligas por usuario
        $maxLeagues = Setting::where('key', 'max_leagues_per_user')->value('value') ?? 3;
        $currentLeagues = $user->leagues()->count();

        if ($currentLeagues >= $maxLeagues) {
            session()->flash('error', __('Has alcanzado el límite máximo de :max ligas', ['max' => $maxLeagues]));
            return;
        }

        // Verificar que la liga esté aprobada y abierta
        if ($league->status !== League::STATUS_APPROVED) {
            session()->flash('error', __('Esta liga no está disponible'));
            return;
        }

        if ($league->is_locked) {
            session()->flash('error', __('Esta liga está cerrada'));
            return;
        }

        // Verificar cupos disponibles
        if ($league->isFull()) {
            session()->flash('error', __('Esta liga está llena'));
            return;
        }

        // Verificar si ya es miembro
        if ($league->hasMember($user->id)) {
            session()->flash('error', __('Ya eres miembro de esta liga'));
            return;
        }

        // Crear membresía
        LeagueMember::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'role' => LeagueMember::ROLE_PARTICIPANT,
            'is_active' => true,
        ]);

        // NUEVO: Crear FantasyTeam automáticamente
        \App\Models\FantasyTeam::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'name' => $user->name . ' Team', // Nombre por defecto
            'budget' => 100.00, // Presupuesto inicial (ajusta según tu lógica)
            'total_points' => 0,
            'is_bot' => false,
        ]);

        session()->flash('success', __('¡Te has unido a la liga exitosamente!'));

        return redirect()->route('manager.onboarding.public-leagues', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        $leagues = League::query()
            ->where('type', League::TYPE_PUBLIC)
            ->where('status', League::STATUS_APPROVED)
            ->where('is_locked', false)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['owner', 'season'])
            ->withCount('fantasyTeams')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.manager.onboarding.public-leagues-list', [
            'leagues' => $leagues
        ]);
    }
}