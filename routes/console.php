<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schedule;
use App\Services\SystemCronService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Cache::forget('weekly_trending_movies');
})->dailyAt('06:00');

Schedule::command('premium:send-renewal-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();

Schedule::call(fn () => app(SystemCronService::class)->run('database-backup'))
    ->dailyAt('02:00')
    ->name('system-database-backup')
    ->withoutOverlapping();

Schedule::call(fn () => app(SystemCronService::class)->run('generate-sitemap'))
    ->dailyAt('03:00')
    ->name('system-generate-sitemap')
    ->withoutOverlapping();

Schedule::call(fn () => app(SystemCronService::class)->run('clean-expired-sessions'))
    ->hourly()
    ->name('system-clean-expired-sessions')
    ->withoutOverlapping();
