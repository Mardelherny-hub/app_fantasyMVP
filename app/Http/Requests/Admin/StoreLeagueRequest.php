<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        // Valores permitidos por tu doc
        $locales = ['es','en','fr'];

        return [
            'name'                        => ['required','string','max:255'],
            // code: se genera automáticamente si no viene; si viene, debe ser único (6 chars recomendado)
            'code'                        => ['nullable','string','max:16', 'unique:leagues,code'],
            'type'                        => ['required', Rule::in([1,2])], // 1 privada / 2 pública
            'owner_user_id'                    => ['required','exists:users,id'],
            'max_participants'            => ['required','integer','min:2','max:40'],
            'auto_fill_bots'              => ['required','boolean'],
            'is_locked'                   => ['nullable','boolean'], // no se edita en create; lo ignoramos si viene
            'locale'                      => ['required', Rule::in($locales)],
            'playoff_teams'               => ['required','integer','min:2','max:20'],
            'playoff_format'              => ['required', Rule::in([1,2])], // 1 Page, 2 Standard
            'regular_season_gameweeks'    => ['required','integer','min:1','max:60'],
            'total_gameweeks'             => ['required','integer','min:1','max:70'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_fill_bots' => (bool) $this->input('auto_fill_bots', true),
        ]);
    }
}
