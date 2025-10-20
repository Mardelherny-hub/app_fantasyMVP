<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Transfer Market Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration values for the Fantasy Football transfer market system.
    |
    */

    // Commission charged on all transactions (5%)
    'commission_rate' => 0.05,

    // Default market settings for new leagues
    'defaults' => [
        'max_multiplier' => 3.00,          // Max 3x market value
        'trade_window_open' => true,       // Market open by default
        'loan_allowed' => false,           // Loans disabled in MVP
        'min_offer_cooldown_h' => 2,       // 2 hours between offers
    ],

    // Offer expiration time (in hours)
    'offer_expiration_hours' => 48,

    // Squad limits
    'squad_limits' => [
        'total' => 23,
        'positions' => [
            1 => ['min' => 1, 'max' => 3],  // GK
            2 => ['min' => 3, 'max' => 8],  // DF
            3 => ['min' => 3, 'max' => 8],  // MF
            4 => ['min' => 1, 'max' => 4],  // FW
        ],
    ],

    // Transfer restrictions
    'max_transfers_per_gameweek' => 3,

    // Price boundaries
    'price_bounds' => [
        'min_multiplier' => 0.5,           // Min 50% of market value
        'max_multiplier' => 3.0,           // Max 300% of market value
    ],

];