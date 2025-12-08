<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('cache:warm-dashboard all')
    ->dailyAt('07:00');

Schedule::command('cache:warm-dashboard month')
    ->hourly()
    ->between('08:00', '18:00');

