<?php

namespace App\Services;

use Native\Laravel\Facades\PowerMonitor;
use Native\Laravel\Facades\System;
use Native\Laravel\Enums\ThermalStatesEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemMonitor
{
    private const CPU_THRESHOLD = 80;
    private const CACHE_TTL = 5; // seconds

    /**
     * Get current CPU usage percentage
     */
    public function getCpuUsage(): float
    {
        return Cache::remember('cpu_usage', self::CACHE_TTL, function () {
            // Get CPU usage through system info
            $cpuInfo = System::cpuUsage();
            $usage = $cpuInfo['percentCPUUsage'] ?? 0;

            // Log warning if exceeds threshold
            if ($usage > self::CPU_THRESHOLD) {
                Log::warning('High CPU usage detected', [
                    'usage' => $usage,
                    'threshold' => self::CPU_THRESHOLD
                ]);
            }

            return $usage;
        });
    }

    /**
     * Get current thermal state
     */
    public function getThermalState(): array
    {
        $state = PowerMonitor::getCurrentThermalState();

        return [
            'state' => $state,
            'label' => $this->getThermalLabel($state),
            'color' => $this->getThermalColor($state),
            'critical' => $state === ThermalStatesEnum::CRITICAL
        ];
    }

    /**
     * Check if system is on AC power
     */
    public function isOnAcPower(): bool
    {
        return PowerMonitor::isOnAcPower();
    }

    /**
     * Get system idle time in seconds
     */
    public function getIdleTime(): int
    {
        return PowerMonitor::getSystemIdleTime();
    }

    /**
     * Get system idle state
     */
    public function getIdleState(): string
    {
        return PowerMonitor::getSystemIdleState();
    }

    /**
     * Get comprehensive system status
     */
    public function getSystemStatus(): array
    {
        $cpuUsage = $this->getCpuUsage();
        $thermal = $this->getThermalState();

        return [
            'cpu' => [
                'usage' => $cpuUsage,
                'high' => $cpuUsage > self::CPU_THRESHOLD,
                'critical' => $cpuUsage > 90
            ],
            'thermal' => $thermal,
            'power' => [
                'ac' => $this->isOnAcPower(),
                'label' => $this->isOnAcPower() ? 'AC Power' : 'Battery'
            ],
            'idle' => [
                'time' => $this->getIdleTime(),
                'state' => $this->getIdleState()
            ],
            'timestamp' => now()->toIso8601String()
        ];
    }

    /**
     * Check if system should throttle performance
     */
    public function shouldThrottle(): bool
    {
        $cpuUsage = $this->getCpuUsage();
        $thermal = $this->getThermalState();

        return $cpuUsage > self::CPU_THRESHOLD ||
               in_array($thermal['state'], [
                   ThermalStatesEnum::SERIOUS,
                   ThermalStatesEnum::CRITICAL
               ]);
    }

    private function getThermalLabel(ThermalStatesEnum $state): string
    {
        return match($state) {
            ThermalStatesEnum::UNKNOWN => 'Unknown',
            ThermalStatesEnum::NOMINAL => 'Normal',
            ThermalStatesEnum::FAIR => 'Fair',
            ThermalStatesEnum::SERIOUS => 'High',
            ThermalStatesEnum::CRITICAL => 'Critical',
            default => 'Unknown'
        };
    }

    private function getThermalColor(ThermalStatesEnum $state): string
    {
        return match($state) {
            ThermalStatesEnum::UNKNOWN => 'gray',
            ThermalStatesEnum::NOMINAL => 'green',
            ThermalStatesEnum::FAIR => 'yellow',
            ThermalStatesEnum::SERIOUS => 'orange',
            ThermalStatesEnum::CRITICAL => 'red',
            default => 'gray'
        };
    }
}