<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFootballMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'season_id' => ['required', 'exists:seasons,id'],
            'matchday' => ['required', 'integer', 'min:1', 'max:30'],
            'home_team_id' => ['required', 'exists:real_teams,id', 'different:away_team_id'],
            'away_team_id' => ['required', 'exists:real_teams,id', 'different:home_team_id'],
            'starts_at' => ['required', 'date'],
            'status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'home_goals' => ['nullable', 'integer', 'min:0'],
            'away_goals' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'season_id' => __('temporada'),
            'matchday' => __('jornada'),
            'home_team_id' => __('equipo local'),
            'away_team_id' => __('equipo visitante'),
            'starts_at' => __('fecha y hora'),
            'status' => __('estado'),
            'home_goals' => __('goles local'),
            'away_goals' => __('goles visitante'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'season_id.required' => __('La temporada es obligatoria.'),
            'season_id.exists' => __('La temporada seleccionada no existe.'),
            'matchday.required' => __('La jornada es obligatoria.'),
            'matchday.min' => __('La jornada debe ser al menos 1.'),
            'matchday.max' => __('La jornada no puede ser mayor a 30.'),
            'home_team_id.required' => __('El equipo local es obligatorio.'),
            'home_team_id.exists' => __('El equipo local seleccionado no existe.'),
            'home_team_id.different' => __('El equipo local debe ser diferente al equipo visitante.'),
            'away_team_id.required' => __('El equipo visitante es obligatorio.'),
            'away_team_id.exists' => __('El equipo visitante seleccionado no existe.'),
            'away_team_id.different' => __('El equipo visitante debe ser diferente al equipo local.'),
            'starts_at.required' => __('La fecha y hora del partido es obligatoria.'),
            'status.in' => __('El estado debe ser: 0 (Pendiente), 1 (En vivo), 2 (Finalizado) o 3 (Pospuesto).'),
            'home_goals.min' => __('Los goles del equipo local no pueden ser negativos.'),
            'away_goals.min' => __('Los goles del equipo visitante no pueden ser negativos.'),
        ];
    }
}