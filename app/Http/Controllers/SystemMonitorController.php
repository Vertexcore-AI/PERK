<?php

namespace App\Http\Controllers;

use App\Services\SystemMonitor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Native\Laravel\Facades\AutoUpdater;
use Illuminate\Support\Facades\Log;

class SystemMonitorController extends Controller
{
    public function __construct(
        private SystemMonitor $systemMonitor
    ) {}

    /**
     * Display the system monitor dashboard
     */
    public function index(): View
    {
        $systemStatus = $this->systemMonitor->getSystemStatus();

        return view('system-monitor.index', compact('systemStatus'));
    }

    /**
     * Get real-time system status data (AJAX endpoint)
     */
    public function getSystemData(): JsonResponse
    {
        $systemStatus = $this->systemMonitor->getSystemStatus();

        return response()->json([
            'success' => true,
            'data' => $systemStatus,
            'shouldThrottle' => $this->systemMonitor->shouldThrottle()
        ]);
    }

    /**
     * Get CPU usage history for charts
     */
    public function getCpuHistory(): JsonResponse
    {
        // Get CPU usage history from cache or logs
        $history = cache()->get('cpu_history', []);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Toggle monitoring on/off
     */
    public function toggleMonitoring(Request $request): JsonResponse
    {
        $enabled = $request->boolean('enabled');

        if ($enabled) {
            // Start monitoring job
            \App\Jobs\MonitorSystemResources::dispatch();
            cache()->put('monitoring_enabled', true, 3600);
        } else {
            // Stop monitoring
            cache()->put('monitoring_enabled', false, 3600);
        }

        return response()->json([
            'success' => true,
            'message' => $enabled ? 'Monitoring started' : 'Monitoring stopped',
            'enabled' => $enabled
        ]);
    }

    /**
     * Get system information
     */
    public function getSystemInfo(): JsonResponse
    {
        $info = [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'user_agent' => request()->userAgent(),
        ];

        return response()->json([
            'success' => true,
            'data' => $info
        ]);
    }

    /**
     * Check for available updates
     */
    public function checkForUpdates(): JsonResponse
    {
        try {
            $updateAvailable = AutoUpdater::checkForUpdates();
            $currentVersion = config('nativephp.version', '1.0.0');

            Log::info('Update check completed', [
                'current_version' => $currentVersion,
                'update_available' => $updateAvailable
            ]);

            return response()->json([
                'success' => true,
                'updateAvailable' => $updateAvailable,
                'currentVersion' => $currentVersion,
                'message' => $updateAvailable ? 'Update available!' : 'You are running the latest version.'
            ]);
        } catch (\Exception $e) {
            Log::error('Update check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to check for updates: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download and install update
     */
    public function installUpdate(): JsonResponse
    {
        try {
            // Check if update is available first
            $updateAvailable = AutoUpdater::checkForUpdates();

            if (!$updateAvailable) {
                return response()->json([
                    'success' => false,
                    'error' => 'No updates available'
                ]);
            }

            // Download and install the update
            Log::info('Starting update installation');

            AutoUpdater::quitAndInstall();

            return response()->json([
                'success' => true,
                'message' => 'Update installation started. Application will restart.'
            ]);
        } catch (\Exception $e) {
            Log::error('Update installation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to install update: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current app version and update status
     */
    public function getVersionInfo(): JsonResponse
    {
        try {
            $currentVersion = config('nativephp.version', '1.0.0');
            $updaterEnabled = config('nativephp.updater.enabled', false);

            return response()->json([
                'success' => true,
                'data' => [
                    'currentVersion' => $currentVersion,
                    'updaterEnabled' => $updaterEnabled,
                    'provider' => config('nativephp.updater.default', 'github'),
                    'appId' => config('nativephp.app_id'),
                    'author' => config('nativephp.author')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}