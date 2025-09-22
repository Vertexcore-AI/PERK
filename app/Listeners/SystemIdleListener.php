<?php

namespace App\Listeners;

use Native\Laravel\Events\PowerMonitor\SystemIdle;
use Native\Laravel\Events\PowerMonitor\SystemLocked;
use Native\Laravel\Events\PowerMonitor\SystemUnlocked;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SystemIdleListener
{
    public function handleSystemIdle(SystemIdle $event): void
    {
        Cache::put('system_status.idle', true, 300); // 5 minutes
        Log::info('System is idle');
    }

    public function handleSystemLocked(SystemLocked $event): void
    {
        Cache::put('system_status.locked', true);
        Log::info('System has been locked');
    }

    public function handleSystemUnlocked(SystemUnlocked $event): void
    {
        Cache::forget('system_status.locked');
        Cache::forget('system_status.idle');
        Log::info('System has been unlocked');
    }
}