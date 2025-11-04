<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRealTeamRequest extends FormRequest
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
            'external_id' => ['nullable', 'integer', 'unique:real_teams,external_id'],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'logo_url' => ['nullable', 'string', 'max:500', 'url'],
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
            'external_id' => __('ID externo'),
            'name' => __('nombre'),
            'short_name' => __('nombre corto'),
            'country' => __('país'),
            'founded_year' => __('año de fundación'),
            'logo_url' => __('URL del logo'),
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
            'external_id.integer' => __('El ID externo debe ser un número entero.'),
            'external_id.unique' => __('Este ID externo ya está registrado.'),
            'name.required' => __('El nombre del equipo es obligatorio.'),
            'short_name.required' => __('El nombre corto es obligatorio.'),
            'short_name.max' => __('El nombre corto no puede tener más de 10 caracteres.'),
            'country.required' => __('El país es obligatorio.'),
            'country.size' => __('El código de país debe tener exactamente 2 caracteres (ISO 3166-1 alpha-2).'),
            'country.regex' => __('El código de país debe estar en mayúsculas (ej: CA, US, MX).'),
            'founded_year.integer' => __('El año de fundación debe ser un número.'),
            'founded_year.min' => __('El año de fundación no puede ser anterior a 1800.'),
            'founded_year.max' => __('El año de fundación no puede ser mayor al año actual.'),
            'logo_url.url' => __('La URL del logo debe ser válida.'),
        ];
    }
}