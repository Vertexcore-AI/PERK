@extends('layouts.app')

@section('title', 'System Resource Monitor')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">System Resource Monitor</h1>
        <div class="flex space-x-3">
            <button id="toggleMonitoring"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <span id="monitoringText">Start Monitoring</span>
            </button>
            <button id="refreshData"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Refresh Data
            </button>
        </div>
    </div>

    <!-- System Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- CPU Usage Card -->
        <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">CPU Usage</h3>
                <span id="cpuPercentage" class="text-2xl font-bold text-blue-600">
                    {{ number_format($systemStatus['cpu']['usage'] ?? 0, 1) }}%
                </span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                <div id="cpuProgress"
                     class="h-3 rounded-full transition-all duration-300 {{ ($systemStatus['cpu']['usage'] ?? 0) > 80 ? 'bg-red-500' : (($systemStatus['cpu']['usage'] ?? 0) > 60 ? 'bg-yellow-500' : 'bg-green-500') }}"
                     style="width: {{ $systemStatus['cpu']['usage'] ?? 0 }}%"></div>
            </div>

            @if(($systemStatus['cpu']['usage'] ?? 0) > 80)
                <div class="flex items-center text-red-600 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    CPU usage above 80% threshold
                </div>
            @endif
        </div>

        <!-- Thermal State Card -->
        <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Thermal State</h3>
                <span id="thermalLabel"
                      class="px-3 py-1 text-sm rounded-full bg-{{ $systemStatus['thermal']['color'] ?? 'gray' }}-100 text-{{ $systemStatus['thermal']['color'] ?? 'gray' }}-800">
                    {{ $systemStatus['thermal']['label'] ?? 'Unknown' }}
                </span>
            </div>

            <div id="thermalIcon" class="text-center text-4xl mb-2">
                @if(isset($systemStatus['thermal']['critical']) && $systemStatus['thermal']['critical'])
                    🔥
                @else
                    🌡️
                @endif
            </div>

            <div id="thermalMessage" class="text-sm text-gray-600 text-center">
                @if(isset($systemStatus['thermal']['critical']) && $systemStatus['thermal']['critical'])
                    Critical temperature detected
                @else
                    Temperature is normal
                @endif
            </div>
        </div>

        <!-- Power Status Card -->
        <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Power Status</h3>
                <span id="powerLabel" class="text-sm font-medium text-gray-600">
                    {{ $systemStatus['power']['label'] ?? 'Unknown' }}
                </span>
            </div>

            <div id="powerIcon" class="text-center text-4xl mb-2">
                @if(isset($systemStatus['power']['ac']) && $systemStatus['power']['ac'])
                    🔌
                @else
                    🔋
                @endif
            </div>

            <div id="powerMessage" class="text-sm text-gray-600 text-center">
                @if(isset($systemStatus['power']['ac']) && $systemStatus['power']['ac'])
                    Running on AC power
                @else
                    Running on battery
                @endif
            </div>
        </div>

        <!-- App Updates Card -->
        <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">App Updates</h3>
                <span id="appVersion" class="text-sm font-medium text-gray-600">
                    v{{ config('nativephp.version', '1.0.0') }}
                </span>
            </div>

            <div id="updateIcon" class="text-center text-4xl mb-2">
                🔄
            </div>

            <div id="updateStatus" class="text-sm text-gray-600 text-center mb-4">
                Ready to check for updates
            </div>

            <div class="space-y-2">
                <button id="checkUpdateBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                    <span id="checkUpdateText">Check for Updates</span>
                </button>

                <button id="installUpdateBtn"
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm hidden">
                    <span id="installUpdateText">Download & Install</span>
                </button>
            </div>

            <div id="updateProgress" class="mt-3 hidden">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Checking for updates...</p>
            </div>
        </div>
    </div>

    <!-- System Details -->
    <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Details</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-sm text-gray-500">Idle Time:</span>
                <div id="idleTime" class="font-medium">
                    {{ isset($systemStatus['idle']['time']) ? gmdate('H:i:s', $systemStatus['idle']['time']) : 'Unknown' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Idle State:</span>
                <div id="idleState" class="font-medium">
                    {{ $systemStatus['idle']['state'] ?? 'Unknown' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Last Updated:</span>
                <div id="lastUpdated" class="font-medium">
                    {{ isset($systemStatus['timestamp']) ? \Carbon\Carbon::parse($systemStatus['timestamp'])->format('H:i:s') : 'Unknown' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Monitoring:</span>
                <div id="monitoringStatus" class="font-medium">
                    <span class="inline-flex items-center">
                        <span id="statusDot" class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                        <span id="statusText">Stopped</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="card p-6 bg-gradient-to-br from-violet-500/5 via-purple-500/5 to-violet-500/5 border border-violet-400/20 backdrop-blur-md shadow-xl shadow-violet-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-violet-500/15">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>

        <div id="systemInfo" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-500">PHP Version:</span>
                <div class="font-medium">{{ PHP_VERSION }}</div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Operating System:</span>
                <div class="font-medium">{{ PHP_OS }}</div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Memory Limit:</span>
                <div class="font-medium">{{ ini_get('memory_limit') }}</div>
            </div>

            <div>
                <span class="text-sm text-gray-500">Execution Time:</span>
                <div class="font-medium">{{ ini_get('max_execution_time') }}s</div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh indicator -->
    <div class="text-center">
        <div class="inline-flex items-center text-sm text-gray-500">
            <div id="refreshIndicator" class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
            <span id="refreshText">Auto-refresh disabled</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
let monitoringInterval = null;
let isMonitoring = false;

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleMonitoring');
    const refreshBtn = document.getElementById('refreshData');
    const checkUpdateBtn = document.getElementById('checkUpdateBtn');
    const installUpdateBtn = document.getElementById('installUpdateBtn');

    toggleBtn.addEventListener('click', toggleMonitoring);
    refreshBtn.addEventListener('click', refreshSystemData);
    checkUpdateBtn.addEventListener('click', checkForUpdates);
    installUpdateBtn.addEventListener('click', installUpdate);

    // Initial data load
    refreshSystemData();
    loadVersionInfo();
});

function toggleMonitoring() {
    const toggleBtn = document.getElementById('toggleMonitoring');
    const monitoringText = document.getElementById('monitoringText');

    if (isMonitoring) {
        stopMonitoring();
    } else {
        startMonitoring();
    }
}

function startMonitoring() {
    isMonitoring = true;

    fetch('{{ route("system-monitor.toggle-monitoring") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ enabled: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('monitoringText').textContent = 'Stop Monitoring';
            document.getElementById('toggleMonitoring').className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors';
            document.getElementById('statusDot').className = 'w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse';
            document.getElementById('statusText').textContent = 'Active';
            document.getElementById('refreshIndicator').className = 'w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse';
            document.getElementById('refreshText').textContent = 'Auto-refreshing every 5 seconds';

            // Start auto-refresh
            monitoringInterval = setInterval(refreshSystemData, 5000);
        }
    })
    .catch(error => console.error('Error starting monitoring:', error));
}

function stopMonitoring() {
    isMonitoring = false;

    fetch('{{ route("system-monitor.toggle-monitoring") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ enabled: false })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('monitoringText').textContent = 'Start Monitoring';
            document.getElementById('toggleMonitoring').className = 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors';
            document.getElementById('statusDot').className = 'w-2 h-2 bg-gray-400 rounded-full mr-2';
            document.getElementById('statusText').textContent = 'Stopped';
            document.getElementById('refreshIndicator').className = 'w-2 h-2 bg-gray-400 rounded-full mr-2';
            document.getElementById('refreshText').textContent = 'Auto-refresh disabled';

            // Stop auto-refresh
            if (monitoringInterval) {
                clearInterval(monitoringInterval);
                monitoringInterval = null;
            }
        }
    })
    .catch(error => console.error('Error stopping monitoring:', error));
}

function refreshSystemData() {
    fetch('{{ route("system-monitor.data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSystemDisplay(data.data);
            }
        })
        .catch(error => console.error('Error fetching system data:', error));
}

function updateSystemDisplay(systemData) {
    // Update CPU usage
    const cpuUsage = systemData.cpu.usage || 0;
    document.getElementById('cpuPercentage').textContent = cpuUsage.toFixed(1) + '%';

    const cpuProgress = document.getElementById('cpuProgress');
    cpuProgress.style.width = cpuUsage + '%';

    // Update CPU progress color
    if (cpuUsage > 80) {
        cpuProgress.className = 'h-3 rounded-full transition-all duration-300 bg-red-500';
    } else if (cpuUsage > 60) {
        cpuProgress.className = 'h-3 rounded-full transition-all duration-300 bg-yellow-500';
    } else {
        cpuProgress.className = 'h-3 rounded-full transition-all duration-300 bg-green-500';
    }

    // Update thermal state
    if (systemData.thermal) {
        document.getElementById('thermalLabel').textContent = systemData.thermal.label;
        document.getElementById('thermalLabel').className = `px-3 py-1 text-sm rounded-full bg-${systemData.thermal.color}-100 text-${systemData.thermal.color}-800`;
        document.getElementById('thermalIcon').textContent = systemData.thermal.critical ? '🔥' : '🌡️';
        document.getElementById('thermalMessage').textContent = systemData.thermal.critical ? 'Critical temperature detected' : 'Temperature is normal';
    }

    // Update power status
    if (systemData.power) {
        document.getElementById('powerLabel').textContent = systemData.power.label;
        document.getElementById('powerIcon').textContent = systemData.power.ac ? '🔌' : '🔋';
        document.getElementById('powerMessage').textContent = systemData.power.ac ? 'Running on AC power' : 'Running on battery';
    }

    // Update system details
    if (systemData.idle) {
        document.getElementById('idleTime').textContent = new Date(systemData.idle.time * 1000).toISOString().substr(11, 8);
        document.getElementById('idleState').textContent = systemData.idle.state;
    }

    // Update last updated time
    document.getElementById('lastUpdated').textContent = new Date(systemData.timestamp).toLocaleTimeString();
}

function loadVersionInfo() {
    fetch('{{ route("system-monitor.version-info") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('appVersion').textContent = 'v' + data.data.currentVersion;
            }
        })
        .catch(error => console.error('Error fetching version info:', error));
}

function checkForUpdates() {
    const checkBtn = document.getElementById('checkUpdateBtn');
    const checkText = document.getElementById('checkUpdateText');
    const updateStatus = document.getElementById('updateStatus');
    const updateIcon = document.getElementById('updateIcon');
    const installBtn = document.getElementById('installUpdateBtn');
    const updateProgress = document.getElementById('updateProgress');

    // Show loading state
    checkText.textContent = 'Checking...';
    checkBtn.disabled = true;
    updateProgress.classList.remove('hidden');
    updateStatus.textContent = 'Checking for updates...';

    fetch('{{ route("system-monitor.check-updates") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateProgress.classList.add('hidden');
        checkBtn.disabled = false;
        checkText.textContent = 'Check for Updates';

        if (data.success) {
            if (data.updateAvailable) {
                updateIcon.textContent = '🔄';
                updateStatus.textContent = 'Update available! Click below to install.';
                installBtn.classList.remove('hidden');
            } else {
                updateIcon.textContent = '✅';
                updateStatus.textContent = data.message;
                installBtn.classList.add('hidden');
            }
        } else {
            updateIcon.textContent = '❌';
            updateStatus.textContent = 'Error: ' + data.error;
            installBtn.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error checking for updates:', error);
        updateProgress.classList.add('hidden');
        checkBtn.disabled = false;
        checkText.textContent = 'Check for Updates';
        updateIcon.textContent = '❌';
        updateStatus.textContent = 'Failed to check for updates';
        installBtn.classList.add('hidden');
    });
}

function installUpdate() {
    const installBtn = document.getElementById('installUpdateBtn');
    const installText = document.getElementById('installUpdateText');
    const updateStatus = document.getElementById('updateStatus');
    const updateIcon = document.getElementById('updateIcon');
    const updateProgress = document.getElementById('updateProgress');

    // Show loading state
    installText.textContent = 'Installing...';
    installBtn.disabled = true;
    updateProgress.classList.remove('hidden');
    updateStatus.textContent = 'Downloading and installing update...';
    updateIcon.textContent = '⬇️';

    fetch('{{ route("system-monitor.install-update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateIcon.textContent = '🔄';
            updateStatus.textContent = data.message;
            updateProgress.classList.add('hidden');

            // Show success message for a few seconds before app restarts
            setTimeout(() => {
                updateStatus.textContent = 'Application will restart shortly...';
            }, 2000);
        } else {
            updateProgress.classList.add('hidden');
            installBtn.disabled = false;
            installText.textContent = 'Download & Install';
            updateIcon.textContent = '❌';
            updateStatus.textContent = 'Error: ' + data.error;
        }
    })
    .catch(error => {
        console.error('Error installing update:', error);
        updateProgress.classList.add('hidden');
        installBtn.disabled = false;
        installText.textContent = 'Download & Install';
        updateIcon.textContent = '❌';
        updateStatus.textContent = 'Failed to install update';
    });
}
</script>
@endpush
@endsection