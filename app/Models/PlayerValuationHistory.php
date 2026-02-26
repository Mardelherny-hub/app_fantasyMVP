<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerValuationHistory extends Model
{
    protected $table = 'player_valuation_history';

    protected $fillable = [
        'player_id',
        'season_id',
        'gameweek_id',
        'market_value',
        'previous_value',
        'source',
    ];

    protected $casts = [
        'market_value' => 'decimal:2',
        'previous_value' => 'decimal:2',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class);
    }
}