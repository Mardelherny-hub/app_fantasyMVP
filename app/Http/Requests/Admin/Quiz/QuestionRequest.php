<?php

namespace App\Http\Requests\Admin\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('quiz.questions.create') 
            || $this->user()->can('quiz.questions.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Datos principales
            'category_id' => ['required', 'exists:quiz_categories,id'],
            'difficulty' => ['required', 'integer', 'in:1,2,3'],
            'is_active' => ['boolean'],
            'meta' => ['nullable', 'array'],
            
            // Traducciones de la pregunta (mínimo ES requerido)
            'translations' => ['required', 'array', 'min:1'],
            'translations.es' => ['required', 'string', 'min:10', 'max:500'],
            'translations.en' => ['nullable', 'string', 'min:10', 'max:500'],
            'translations.fr' => ['nullable', 'string', 'min:10', 'max:500'],
            
            // Opciones (4 opciones obligatorias)
            'options' => ['required', 'array', 'size:4'],
            'options.*.translations' => ['required', 'array'],
            'options.*.translations.es' => ['required', 'string', 'min:1', 'max:200'],
            'options.*.translations.en' => ['nullable', 'string', 'min:1', 'max:200'],
            'options.*.translations.fr' => ['nullable', 'string', 'min:1', 'max:200'],
            'options.*.is_correct' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'difficulty.required' => 'La dificultad es obligatoria.',
            'difficulty.in' => 'La dificultad debe ser: 1 (Fácil), 2 (Media) o 3 (Difícil).',
            
            'translations.required' => 'Debe proporcionar al menos una traducción.',
            'translations.es.required' => 'La traducción en español es obligatoria.',
            'translations.es.min' => 'La pregunta debe tener al menos 10 caracteres.',
            'translations.es.max' => 'La pregunta no puede superar 500 caracteres.',
            
            'options.required' => 'Debe proporcionar 4 opciones de respuesta.',
            'options.size' => 'Debe proporcionar exactamente 4 opciones.',
            'options.*.translations.es.required' => 'Todas las opciones deben tener traducción en español.',
            'options.*.is_correct.required' => 'Debe marcar cuál es la opción correcta.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que is_active sea booleano
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que haya EXACTAMENTE una opción correcta
            $options = $this->input('options', []);
            $correctCount = collect($options)->where('is_correct', true)->count();
            
            if ($correctCount === 0) {
                $validator->errors()->add('options', 'Debe marcar al menos una opción como correcta.');
            } elseif ($correctCount > 1) {
                $validator->errors()->add('options', 'Solo puede haber una opción correcta.');
            }
        });
    }
}