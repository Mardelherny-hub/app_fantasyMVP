<?php

namespace App\Livewire\Manager\Onboarding;

use App\Models\League;
use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use App\Models\Setting;
use Livewire\Component;

class JoinWithCode extends Component
{
    public $code = '';

    protected $rules = [
        'code' => 'required|string|min:6|max:10',
    ];

    protected $messages = [
        'code.required' => 'El código es obligatorio',
        'code.min' => 'El código debe tener al menos 6 caracteres',
        'code.max' => 'El código no puede tener más de 10 caracteres',
    ];

    public function joinWithCode()
    {
        $this->validate();

        $user = auth()->user();

        // Buscar liga por código
        $league = League::where('code', strtoupper($this->code))->first();

        if (!$league) {
            $this->addError('code', __('Código de liga no válido'));
            return;
        }

        // Verificar límite de ligas por usuario
        $maxLeagues = Setting::where('key', 'max_leagues_per_user')->value('value') ?? 3;
        $currentLeagues = $user->leagues()->count();

        if ($currentLeagues >= $maxLeagues) {
            session()->flash('error', __('Has alcanzado el límite máximo de :max ligas', ['max' => $maxLeagues]));
            return;
        }

        // Verificar que la liga esté aprobada
        if ($league->status !== League::STATUS_APPROVED) {
            $this->addError('code', __('Esta liga no está disponible (pendiente de aprobación)'));
            return;
        }

        // Verificar que la liga esté abierta
        if ($league->is_locked) {
            $this->addError('code', __('Esta liga está cerrada y no acepta nuevos miembros'));
            return;
        }

        // Verificar cupos disponibles
        if ($league->isFull()) {
            $this->addError('code', __('Esta liga está llena'));
            return;
        }

        // Verificar si ya es miembro
        if ($league->hasMember($user->id)) {
            $this->addError('code', __('Ya eres miembro de esta liga'));
            return;
        }

        // Crear membresía con deadline de 72 horas
        $member = LeagueMember::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'role' => LeagueMember::ROLE_PARTICIPANT,
            'is_active' => true,
            'squad_deadline_at' => now()->addHours(72), // NUEVO: Deadline de 72 horas
        ]);

        // Crear FantasyTeam automáticamente
        FantasyTeam::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'name' => $user->name . ' Team', // Nombre por defecto
            'budget' => 100.00,
            'total_points' => 0,
            'is_bot' => false,
        ]);

        session()->flash('success', __('¡Te has unido a la liga :name exitosamente! Tienes 72 horas para armar tu plantilla.', ['name' => $league->name]));
        
        return redirect()->route('manager.dashboard', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        return view('livewire.manager.onboarding.join-with-code');
    }
}