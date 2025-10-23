<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFixturesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'league_id' => 'required|exists:leagues,id',
            'type' => 'required|in:regular,playoffs',
            'start_gameweek' => 'nullable|integer|min:1|max:27',
            'quarter_gameweek' => 'nullable|integer|min:28|max:28',
            'semi_gameweek' => 'nullable|integer|min:29|max:29',
            'final_gameweek' => 'nullable|integer|min:30|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'league_id.required' => 'Debe seleccionar una liga',
            'league_id.exists' => 'La liga seleccionada no existe',
            'type.required' => 'Debe seleccionar el tipo de fixtures',
            'type.in' => 'Tipo inválido. Use: regular o playoffs',
            'start_gameweek.min' => 'El gameweek inicial debe ser al menos 1',
            'start_gameweek.max' => 'El gameweek inicial no puede ser mayor a 27',
        ];
    }
}