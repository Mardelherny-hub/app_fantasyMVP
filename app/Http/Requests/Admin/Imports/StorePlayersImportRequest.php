<?php

namespace App\Http\Requests\Admin\Imports;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayersImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return  true; // o policy/role
    }

    public function rules(): array
    {
        return [
            'file' => ['required','file','mimes:csv,txt,xlsx','max:20480'], // aceptamos CSV y XLSX
            'mode' => ['required','in:create,upsert,update'], // estrategia
            'id_column' => ['nullable','string'],            // para update/upsert
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Subí un CSV (recomendado) o XLSX.',
        ];
    }
}
