<?php

namespace App\Listeners;

use Native\Laravel\Events\PowerMonitor\ThermalStateChanged;
use Native\Laravel\Enums\ThermalStatesEnum;
use Native\Laravel\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ThermalStateListener
{
    public function handle(ThermalStateChanged $event): void
    {
        $state = $event->thermalState;

        Log::info('Thermal state changed', [
            'state' => $state->value,
            'timestamp' => now()
        ]);

        match($state) {
            ThermalStatesEnum::SERIOUS => $this->handleSeriousState(),
            ThermalStatesEnum::CRITICAL => $this->handleCriticalState(),
            default => null
        };
    }

    private function handleSeriousState(): void
    {
        Notification::title('High Temperature Warning')
            ->message('System temperature is elevated. Consider closing resource-intensive applications.')
            ->show();

        Log::warning('System thermal state is SERIOUS');
    }

    private function handleCriticalState(): void
    {
        Notification::title('Critical Temperature Alert')
            ->message('System temperature is critical! Performance may be throttled to prevent damage.')
            ->show();

        Log::critical('System thermal state is CRITICAL');
    }
}