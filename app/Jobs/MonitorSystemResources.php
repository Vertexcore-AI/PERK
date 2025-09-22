<?php

namespace App\Jobs;

use App\Services\SystemMonitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Native\Laravel\Facades\Notification;

class MonitorSystemResources implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(SystemMonitor $systemMonitor): void
    {
        $status = $systemMonitor->getSystemStatus();

        // Log current status
        Log::info('System resource check', [
            'cpu_usage' => $status['cpu']['usage'],
            'thermal_state' => $status['thermal']['label'],
            'power_source' => $status['power']['label'],
            'timestamp' => $status['timestamp']
        ]);

        // Check for high CPU usage
        if ($status['cpu']['high']) {
            $this->handleHighCpuUsage($status['cpu']);
        }

        // Check for critical thermal state
        if ($status['thermal']['critical']) {
            $this->handleCriticalThermal();
        }

        // Check if system should throttle
        if ($systemMonitor->shouldThrottle()) {
            $this->handlePerformanceThrottle($status);
        }

        // Schedule next check
        self::dispatch()->delay(now()->addSeconds(30));
    }

    private function handleHighCpuUsage(array $cpuStatus): void
    {
        $message = $cpuStatus['critical'] ?
            'Critical CPU usage detected! Performance may be severely impacted.' :
            'High CPU usage detected. Consider closing unnecessary applications.';

        Notification::title('CPU Usage Warning')
            ->message($message)
            ->show();

        Log::warning('High CPU usage detected', $cpuStatus);
    }

    private function handleCriticalThermal(): void
    {
        Notification::title('Temperature Critical')
            ->message('System temperature is critical! Performance will be throttled to prevent damage.')
            ->show();

        Log::critical('Critical thermal state detected');
    }

    private function handlePerformanceThrottle(array $status): void
    {
        Log::warning('Performance throttling recommended', [
            'cpu_usage' => $status['cpu']['usage'],
            'thermal_state' => $status['thermal']['label'],
            'reason' => 'CPU or thermal threshold exceeded'
        ]);

        // Here you could implement actual performance throttling
        // For example: reduce worker processes, delay heavy operations, etc.
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('System monitoring job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Restart monitoring after failure
        self::dispatch()->delay(now()->addMinutes(1));
    }
}