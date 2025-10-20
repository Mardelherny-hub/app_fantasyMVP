<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\Manager\Market\ProcessExpiredOffersJob;
use App\Jobs\Manager\Market\ProcessExpiredListingsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ========================================
// SQUAD BUILDER SCHEDULED TASKS
// ========================================

/**
 * Process expired squad building deadlines every hour.
 * 
 * This command checks for fantasy teams that haven't completed
 * their squad within the 72-hour deadline and automatically
 * assigns players to their roster.
 * 
 * Runs: Every hour at minute 0
 * Example: 00:00, 01:00, 02:00, etc.
 */
Schedule::command('squad:process-expired-deadlines')
    ->hourly()
    ->withoutOverlapping();

// ========================================
// TRANSFER MARKET SCHEDULED TASKS
// ========================================

/**
 * Process expired offers every hour.
 * 
 * This job marks pending offers as expired after 48 hours
 * from their creation date.
 * 
 * Runs: Every hour at minute 0
 * Example: 00:00, 01:00, 02:00, etc.
 */
Schedule::job(new ProcessExpiredOffersJob)
    ->hourly()
    ->withoutOverlapping();

/**
 * Process expired listings every 6 hours.
 * 
 * This job marks active listings as expired if they have
 * an expires_at date in the past, and rejects all their
 * pending offers.
 * 
 * Runs: Every 6 hours at minute 0
 * Example: 00:00, 06:00, 12:00, 18:00
 */
Schedule::job(new ProcessExpiredListingsJob)
    ->everySixHours()
    ->withoutOverlapping();

/**
 * Update market prices daily.
 * 
 * This command updates player market values based on:
 * - Performance factor: Average points from last 5 gameweeks (70% weight)
 * - Demand factor: Recent offers and transfers (30% weight)
 * 
 * Runs: Daily at 3:00 AM
 */
Schedule::command('market:update-prices')
    ->dailyAt('03:00')
    ->withoutOverlapping();