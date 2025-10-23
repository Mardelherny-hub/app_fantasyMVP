<?php

namespace App\Livewire\Manager\Settings;

use App\Models\FantasyTeam;
use App\Models\League;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TeamSettings extends Component
{
    use WithFileUploads;

    // Equipo del usuario
    public FantasyTeam $team;
    public League $league;
    public User $user;
    
    // Datos del formulario
    public string $teamName;
    public ?string $teamSlogan = null;
    public ?string $emblemUrl = null;
    
    // Preferencias del usuario
    public bool $emailNotifications = true;
    public string $locale = 'es';
    
    // UI State
    public bool $saving = false;
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    /**
     * Reglas de validación
     */
    protected function rules()
    {
        return [
            'teamName' => 'required|string|max:50|unique:fantasy_teams,name,' . $this->team->id,
            'teamSlogan' => 'nullable|string|max:100',
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    protected $messages = [
        'teamName.required' => 'El nombre del equipo es obligatorio.',
        'teamName.max' => 'El nombre no puede superar los 50 caracteres.',
        'teamName.unique' => 'Este nombre ya está en uso.',
        'teamSlogan.max' => 'El eslogan no puede superar los 100 caracteres.',
    ];

    /**
     * Inicializar componente
     */
    public function mount()
    {
        $this->user = auth()->user();
        
        // Obtener equipo del manager
        $this->team = FantasyTeam::where('user_id', $this->user->id)
            ->whereNotNull('league_id')
            ->with('league.season')
            ->firstOrFail();
        
        $this->league = $this->team->league;
        
        // Cargar datos actuales
        $this->teamName = $this->team->name;
        $this->teamSlogan = $this->team->slogan ?? '';
        $this->emblemUrl = $this->team->emblem_url;
        
        // Preferencias del usuario
        $this->locale = app()->getLocale();
    }

    /**
     * Guardar configuraciones del equipo
     */
    public function saveTeamSettings()
    {
        $this->saving = true;
        $this->successMessage = null;
        $this->errorMessage = null;
        
        try {
            // Validar
            $this->validate();
            
            // Actualizar equipo
            $this->team->update([
                'name' => $this->teamName,
                'slug' => Str::slug($this->teamName),
                'slogan' => $this->teamSlogan,
            ]);
            
            $this->successMessage = __('Configuración guardada exitosamente');
            
            // Refrescar datos
            $this->team->refresh();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->first();
        } catch (\Exception $e) {
            $this->errorMessage = __('Error al guardar la configuración');
        } finally {
            $this->saving = false;
        }
    }

    /**
     * Copiar código de liga al portapapeles
     */
    public function copyLeagueCode()
    {
        // Esto se maneja con JavaScript en el frontend
        $this->dispatch('code-copied');
    }

    /**
     * Obtener código de invitación de la liga
     */
    public function getLeagueCode(): ?string
    {
        return $this->league->invitation_code ?? null;
    }

    /**
     * Verificar si la liga es privada
     */
    public function isPrivateLeague(): bool
    {
        return $this->league->type === League::TYPE_PRIVATE;
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.settings.team-settings');
    }
}