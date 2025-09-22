<?php

namespace App\Providers;

use App\Listeners\PowerStateListener;
use App\Listeners\SystemIdleListener;
use App\Listeners\ThermalStateListener;
use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\PowerMonitor;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Events\PowerMonitor\OnAcPower;
use Native\Laravel\Events\PowerMonitor\OnBatteryPower;
use Native\Laravel\Events\PowerMonitor\SystemIdle;
use Native\Laravel\Events\PowerMonitor\SystemLocked;
use Native\Laravel\Events\PowerMonitor\SystemUnlocked;
use Native\Laravel\Events\PowerMonitor\ThermalStateChanged;
use Illuminate\Support\Facades\Event;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Open main window with optimized settings
        Window::open()
            ->width(1200)
            ->height(800)
            ->minWidth(800)
            ->minHeight(600)
            ->rememberState();

        // Register power monitor event listeners
        $this->registerPowerMonitorEvents();

        // Start monitoring system resources
        PowerMonitor::onThermalStateChange();
        PowerMonitor::onPowerStateChange();
        PowerMonitor::onSystemIdleStateChange();
    }

    /**
     * Register power monitor event listeners
     */
    private function registerPowerMonitorEvents(): void
    {
        Event::listen(ThermalStateChanged::class, ThermalStateListener::class);
        Event::listen(OnAcPower::class, [PowerStateListener::class, 'handleAcPower']);
        Event::listen(OnBatteryPower::class, [PowerStateListener::class, 'handleBatteryPower']);
        Event::listen(SystemIdle::class, [SystemIdleListener::class, 'handleSystemIdle']);
        Event::listen(SystemLocked::class, [SystemIdleListener::class, 'handleSystemLocked']);
        Event::listen(SystemUnlocked::class, [SystemIdleListener::class, 'handleSystemUnlocked']);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '256M',
            'max_execution_time' => '300',
            'opcache.enable' => '1',
            'opcache.enable_cli' => '1',
            'opcache.memory_consumption' => '128',
            'opcache.max_accelerated_files' => '4000',
        ];
    }
}
