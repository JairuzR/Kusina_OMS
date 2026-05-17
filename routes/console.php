<?php

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dynamic backup schedule driven by Site Settings
$schedule = Setting::get('backup_schedule', 'weekly');

match ($schedule) {
    'daily'   => Schedule::command('backup:database --type=scheduled')->dailyAt('02:00'),
    'monthly' => Schedule::command('backup:database --type=scheduled')->monthlyOn(1, '02:00'),
    default   => Schedule::command('backup:database --type=scheduled')->weekly()->mondays()->at('02:00'),
};