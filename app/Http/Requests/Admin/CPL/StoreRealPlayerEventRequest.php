<?php

namespace App\Http\Requests\Admin\CPL;

use App\Models\RealPlayerEvent;
use Illuminate\Foundation\Http\FormRequest;

class StoreRealPlayerEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'real_player_id' => 'required|exists:real_players,id',
            'real_team_id' => 'required|exists:real_teams,id',
            'type' => 'required|in:goal,assist,yellow,red,own_goal,penalty_scored,penalty_missed,sub_in,sub_out',
            'minute' => 'required|integer|min:0|max:120',
        ];
    }
}