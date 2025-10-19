<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Log::info('Squad deadline processing completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Squad deadline processing failed');
    });