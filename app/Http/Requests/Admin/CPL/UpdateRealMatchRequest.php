<?php

namespace App\Http\Requests\Admin\CPL;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRealMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'real_fixture_id' => 'required|exists:real_fixtures,id',
            'status' => 'required|in:live,ht,ft,finished,postponed,canceled',
            'minute' => 'nullable|integer|min:0|max:120',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'started_at_utc' => 'nullable|date',
            'finished_at_utc' => 'nullable|date|after_or_equal:started_at_utc',
        ];
    }
}