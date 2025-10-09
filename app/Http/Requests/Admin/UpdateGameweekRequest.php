<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameweekRequest extends FormRequest
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
        $gameweekId = $this->route('gameweek')->id;

        return [
            'season_id' => ['required', 'exists:seasons,id'],
            'number' => [
                'required', 
                'integer', 
                'min:1', 
                'max:30',
                Rule::unique('gameweeks')->where(function ($query) {
                    return $query->where('season_id', $this->season_id);
                })->ignore($gameweekId)
            ],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_closed' => ['nullable', 'boolean'],
            'is_playoff' => ['nullable', 'boolean'],
            'playoff_round' => ['nullable', 'integer', 'in:1,2,3'],
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
            'number' => __('número de jornada'),
            'starts_at' => __('fecha de inicio'),
            'ends_at' => __('fecha de fin'),
            'is_closed' => __('cerrada'),
            'is_playoff' => __('playoff'),
            'playoff_round' => __('ronda de playoff'),
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
            'number.required' => __('El número de jornada es obligatorio.'),
            'number.unique' => __('Ya existe una jornada con este número en la temporada seleccionada.'),
            'number.min' => __('El número de jornada debe ser al menos 1.'),
            'number.max' => __('El número de jornada no puede ser mayor a 30.'),
            'starts_at.required' => __('La fecha de inicio es obligatoria.'),
            'ends_at.required' => __('La fecha de fin es obligatoria.'),
            'ends_at.after' => __('La fecha de fin debe ser posterior a la fecha de inicio.'),
            'playoff_round.in' => __('La ronda de playoff debe ser: 1 (Cuartos), 2 (Semifinales) o 3 (Final).'),
        ];
    }
}