<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'known_as' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'in:1,2,3,4'],
            'nationality' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'height_cm' => ['nullable', 'integer', 'min:150', 'max:220'],
            'weight_kg' => ['nullable', 'integer', 'min:50', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500', 'url'],
            'is_active' => ['nullable', 'boolean'],
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
            'full_name' => __('nombre completo'),
            'known_as' => __('nombre conocido'),
            'position' => __('posición'),
            'nationality' => __('nacionalidad'),
            'birthdate' => __('fecha de nacimiento'),
            'height_cm' => __('altura'),
            'weight_kg' => __('peso'),
            'photo_url' => __('URL de foto'),
            'is_active' => __('activo'),
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
            'full_name.required' => __('El nombre completo del jugador es obligatorio.'),
            'position.required' => __('La posición del jugador es obligatoria.'),
            'position.in' => __('La posición debe ser: 1 (GK), 2 (DF), 3 (MF) o 4 (FW).'),
            'nationality.size' => __('El código de nacionalidad debe tener exactamente 2 caracteres (ISO 3166-1 alpha-2).'),
            'nationality.regex' => __('El código de nacionalidad debe estar en mayúsculas (ej: CA, US, MX).'),
            'birthdate.before' => __('La fecha de nacimiento debe ser en el pasado.'),
            'height_cm.min' => __('La altura debe ser al menos 150 cm.'),
            'height_cm.max' => __('La altura no puede ser mayor a 220 cm.'),
            'weight_kg.min' => __('El peso debe ser al menos 50 kg.'),
            'weight_kg.max' => __('El peso no puede ser mayor a 120 kg.'),
            'photo_url.url' => __('La URL de la foto debe ser válida.'),
        ];
    }
}