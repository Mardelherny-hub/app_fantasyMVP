<?php

namespace App\Livewire\Manager\Onboarding;

use App\Models\League;
use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use App\Models\Season;
use App\Models\Setting;
use Livewire\Component;

class CreatePrivateLeague extends Component
{
    public $name = '';
    public $max_participants = 10;
    public $locale = 'es';

    protected $rules = [
        'name' => 'required|string|min:3|max:100',
        'max_participants' => 'required|integer|min:4|max:20',
        'locale' => 'required|in:es,en,fr',
    ];

    protected $messages = [
        'name.required' => 'El nombre de la liga es obligatorio',
        'name.min' => 'El nombre debe tener al menos 3 caracteres',
        'name.max' => 'El nombre no puede tener más de 100 caracteres',
        'max_participants.required' => 'El número de participantes es obligatorio',
        'max_participants.min' => 'Mínimo 4 participantes',
        'max_participants.max' => 'Máximo 20 participantes',
        'locale.required' => 'El idioma es obligatorio',
        'locale.in' => 'Idioma no válido',
    ];

    public function mount()
    {
        $this->locale = app()->getLocale();
        
        // Obtener default de settings
        $defaultSize = Setting::where('key', 'default_league_size')->value('value');
        if ($defaultSize) {
            $this->max_participants = (int) $defaultSize;
        }
    }

    public function createLeague()
    {
        $this->validate();

        $user = auth()->user();

        // Verificar límite de ligas por usuario
        $maxLeagues = Setting::where('key', 'max_leagues_per_user')->value('value') ?? 3;
        $currentLeagues = $user->leagues()->count();

        if ($currentLeagues >= $maxLeagues) {
            session()->flash('error', __('Has alcanzado el límite máximo de :max ligas', ['max' => $maxLeagues]));
            return;
        }

        // Obtener temporada activa
        $season = Season::where('is_active', true)->first();
        
        if (!$season) {
            $season = Season::orderBy('starts_at', 'desc')->first();
        }

        if (!$season) {
            session()->flash('error', __('No hay temporadas disponibles. Contacta al administrador.'));
            return;
        }

        // Verificar si requiere aprobación
        $requireApproval = Setting::where('key', 'require_league_approval')->value('value') ?? 1;

        // Crear liga con status pending_approval
        $league = League::create([
            'owner_user_id' => $user->id,
            'season_id' => $season->id,
            'name' => $this->name,
            'type' => League::TYPE_PRIVATE,
            'max_participants' => $this->max_participants,
            'auto_fill_bots' => true,
            'is_locked' => false,
            'status' => $requireApproval ? League::STATUS_PENDING_APPROVAL : League::STATUS_APPROVED,
            'locale' => $this->locale,
            'playoff_teams' => 5,
            'playoff_format' => League::PLAYOFF_FORMAT_PAGE,
            'regular_season_gameweeks' => 27,
            'total_gameweeks' => 30,
        ]);

        // Crear membresía del owner como MANAGER con deadline de 72 horas
        $member = LeagueMember::create([
            'league_id' => $league->id,
            'user_id' => $user->id,
            'role' => LeagueMember::ROLE_MANAGER,
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

        // Redirect según el status
        if ($requireApproval) {
            session()->flash('info', __('Tu liga ha sido creada y está pendiente de aprobación. Tienes 72 horas para armar tu plantilla una vez sea aprobada.'));
            return redirect()->route('manager.onboarding.pending-approval', ['locale' => app()->getLocale()]);
        } else {
            session()->flash('success', __('¡Liga creada exitosamente! Tienes 72 horas para armar tu plantilla.'));
            return redirect()->route('manager.dashboard', ['locale' => app()->getLocale()]);
        }
    }

    public function render()
    {
        return view('livewire.manager.onboarding.create-private-league');
    }
}