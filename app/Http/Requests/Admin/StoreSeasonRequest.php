<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeasonRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:seasons,code'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
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
            'name' => __('nombre'),
            'code' => __('código'),
            'starts_at' => __('fecha de inicio'),
            'ends_at' => __('fecha de fin'),
            'is_active' => __('activa'),
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
            'name.required' => __('El nombre es obligatorio.'),
            'code.required' => __('El código es obligatorio.'),
            'code.unique' => __('Ya existe una temporada con este código.'),
            'starts_at.required' => __('La fecha de inicio es obligatoria.'),
            'ends_at.required' => __('La fecha de fin es obligatoria.'),
            'ends_at.after' => __('La fecha de fin debe ser posterior a la fecha de inicio.'),
        ];
    }
}