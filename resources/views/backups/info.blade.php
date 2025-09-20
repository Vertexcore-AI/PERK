@extends('layouts.app')

@section('title', 'Backup Information')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Backup Information</h1>
        <a href="{{ route('backups.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Backups</a>
    </div>

    <!-- Backup File Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">File Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Filename</label>
                <p class="text-sm text-gray-900 font-mono">{{ $filename }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $validation['valid'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $validation['valid'] ? 'Valid' : 'Invalid' }}
                </span>
            </div>
        </div>

        @if(!$validation['valid'])
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-red-800">Validation Error</h3>
                <p class="text-sm text-red-700 mt-1">{{ $validation['error'] }}</p>
            </div>
        @endif
    </div>

    <!-- Backup Metadata -->
    @if($info)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Backup Metadata</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Backup Date</label>
                    <p class="text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($info['backup_date'])->format('Y-m-d H:i:s') }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Database Size</label>
                    <p class="text-sm text-gray-900">
                        {{ number_format($info['database_size'] / 1024 / 1024, 2) }} MB
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Laravel Version</label>
                    <p class="text-sm text-gray-900">{{ $info['laravel_version'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Backup Type</label>
                    <p class="text-sm text-gray-900">{{ $info['backup_type'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Original Database</label>
                    <p class="text-sm text-gray-900">{{ $info['original_name'] }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-yellow-800">No Metadata Available</h3>
            <p class="text-sm text-yellow-700 mt-1">
                This backup does not contain metadata information. It may be from an older version or created externally.
            </p>
        </div>
    @endif

    <!-- Actions -->
    @if($validation['valid'])
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
            <div class="flex space-x-4">
                <a href="{{ route('backups.download', $filename) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Download Backup
                </a>

                <button onclick="restoreBackup('{{ storage_path('app/backups/' . $filename) }}')"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                    Restore Database
                </button>

                <form action="{{ route('backups.destroy', $filename) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
                            onclick="return confirm('Are you sure you want to delete this backup?')">
                        Delete Backup
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Technical Details -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Technical Details</h2>
        <div class="text-sm text-gray-600 space-y-2">
            <p><strong>Format:</strong> ZIP archive containing SQLite database file</p>
            <p><strong>Compression:</strong> Standard ZIP compression</p>
            <p><strong>Compatibility:</strong> Compatible with PERK backup/restore system</p>
            <p><strong>Security:</strong> No encryption applied to backup files</p>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Restore Database</h3>
        <p class="text-gray-600 mb-4">This will replace your current database with this backup. A backup of the current database will be created automatically.</p>

        <form id="restoreForm" method="POST" action="{{ route('backups.restore') }}">
            @csrf
            <input type="hidden" name="backup_path" id="backup_path">

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="create_current_backup" value="1" checked class="mr-2">
                    Create backup of current database before restore
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRestoreModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                    Restore
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function restoreBackup(path) {
    document.getElementById('backup_path').value = path;
    document.getElementById('restoreModal').classList.remove('hidden');
    document.getElementById('restoreModal').classList.add('flex');
}

function closeRestoreModal() {
    document.getElementById('restoreModal').classList.add('hidden');
    document.getElementById('restoreModal').classList.remove('flex');
}
</script>
@endsection