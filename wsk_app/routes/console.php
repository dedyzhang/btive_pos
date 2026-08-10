<?php

use App\Models\Settings;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fires once, in the minute the admin configured in Settings > Notifikasi Laporan Pendapatan.
// Requires the server cron entry: * * * * * php artisan schedule:run
Schedule::command('report:send-daily-revenue')
    ->everyMinute()
    ->when(function () {
        $setting = Settings::where('jenis', 'daily_revenue_notification')->first();
        if (!$setting || !$setting->nilai) {
            return false;
        }

        $data = @unserialize($setting->nilai) ?: [];

        return !empty($data['enabled']) && !empty($data['time']) && now()->format('H:i') === $data['time'];
    })
    ->withoutOverlapping();
