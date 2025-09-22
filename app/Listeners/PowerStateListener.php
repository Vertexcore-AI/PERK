<?php

namespace App\Listeners;

use Native\Laravel\Events\PowerMonitor\OnAcPower;
use Native\Laravel\Events\PowerMonitor\OnBatteryPower;
use Native\Laravel\Facades\Notification;
use Illuminate\Support\Facades\Log;

class PowerStateListener
{
    public function handleAcPower(OnAcPower $event): void
    {
        Log::info('Switched to AC power');

        Notification::title('Power Status')
            ->message('Running on AC power - Full performance available')
            ->show();
    }

    public function handleBatteryPower(OnBatteryPower $event): void
    {
        Log::info('Switched to battery power');

        Notification::title('Power Status')
            ->message('Running on battery - Performance may be optimized for power saving')
            ->show();
    }
}