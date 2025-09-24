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
            try {
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
            } catch (\Exception $e) {
                Log::error('Failed to get CPU usage', ['error' => $e->getMessage()]);
                return 0.0; // Return safe default
            }
        });
    }

    /**
     * Get current thermal state
     */
    public function getThermalState(): array
    {
        try {
            $state = PowerMonitor::getCurrentThermalState();

            return [
                'state' => $state,
                'label' => $this->getThermalLabel($state),
                'color' => $this->getThermalColor($state),
                'critical' => $state === ThermalStatesEnum::CRITICAL
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get thermal state', ['error' => $e->getMessage()]);
            return [
                'state' => ThermalStatesEnum::UNKNOWN,
                'label' => 'Unknown',
                'color' => 'gray',
                'critical' => false
            ];
        }
    }

    /**
     * Check if system is on AC power
     */
    public function isOnAcPower(): bool
    {
        try {
            return PowerMonitor::isOnAcPower();
        } catch (\Exception $e) {
            Log::error('Failed to get AC power status', ['error' => $e->getMessage()]);
            return true; // Safe default
        }
    }

    /**
     * Get system idle time in seconds
     */
    public function getIdleTime(): int
    {
        try {
            return PowerMonitor::getSystemIdleTime();
        } catch (\Exception $e) {
            Log::error('Failed to get idle time', ['error' => $e->getMessage()]);
            return 0; // Safe default
        }
    }

    /**
     * Get system idle state
     */
    public function getIdleState(): string
    {
        try {
            return PowerMonitor::getSystemIdleState();
        } catch (\Exception $e) {
            Log::error('Failed to get idle state', ['error' => $e->getMessage()]);
            return 'unknown'; // Safe default
        }
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