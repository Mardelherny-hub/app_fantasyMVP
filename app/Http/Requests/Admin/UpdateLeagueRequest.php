<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'owner_user_id'            => $this->input('owner_user_id', $this->input('owner_id')),
            'type'                     => $this->has('type') ? (int) $this->input('type') : null,
            'playoff_format'           => $this->has('playoff_format') ? (int) $this->input('playoff_format') : null,
            'max_participants'         => $this->has('max_participants') ? (int) $this->input('max_participants') : null,
            'playoff_teams'            => $this->has('playoff_teams') ? (int) $this->input('playoff_teams') : null,
            'regular_season_gameweeks' => $this->has('regular_season_gameweeks') ? (int) $this->input('regular_season_gameweeks') : null,
            'total_gameweeks'          => $this->has('total_gameweeks') ? (int) $this->input('total_gameweeks') : null,
            'season_id'                => $this->filled('season_id') ? (int) $this->input('season_id') : null,
            'auto_fill_bots'           => (bool) $this->boolean('auto_fill_bots'),
        ]);
    }

    public function rules(): array
    {
        $locales = ['es','en','fr'];
        $leagueId = $this->route('league')->id;

        return [
            'name'                     => ['required','string','max:255'],
            'code'                     => ['nullable','string','max:16', Rule::unique('leagues','code')->ignore($leagueId)],
            'type'                     => ['required', Rule::in([1,2])],
            'owner_user_id'            => ['required','exists:users,id'], // <-- aquí
            'season_id'                => ['nullable','exists:seasons,id'],
            'max_participants'         => ['required','integer','min:2','max:40'],
            'auto_fill_bots'           => ['required','boolean'],
            'locale'                   => ['required', Rule::in($locales)],
            'playoff_teams'            => ['required','integer','min:2','max:20'],
            'playoff_format'           => ['required', Rule::in([1,2])],
            'regular_season_gameweeks' => ['required','integer','min:1','max:60'],
            'total_gameweeks'          => ['required','integer','min:1','max:70'],
        ];
    }

    public function attributes(): array
    {
        return [
            'owner_user_id' => __('Propietario'),
        ];
    }
}
