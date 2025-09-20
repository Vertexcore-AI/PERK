@extends('layouts.app')

@section('title', 'Database Backups')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Database Backups</h1>
        <div class="flex space-x-3">
            <a href="{{ route('backups.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Create Backup
            </a>
            <a href="{{ route('backups.restore-history') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Restore History
            </a>
        </div>
    </div>

    <!-- External Path Filter -->
    <div class="bg-white p-4 rounded-lg shadow">
        <form method="GET" action="{{ route('backups.index') }}" class="flex items-end space-x-4">
            <div class="flex-1">
                <label for="external_path" class="block text-sm font-medium text-gray-700 mb-1">External Backup Path</label>
                <input type="text" name="external_path" id="external_path"
                       value="{{ $externalPath }}"
                       placeholder="Enter external path to view backups from external location"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Filter
            </button>
        </form>
    </div>

    <!-- Upload Backup -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-3">Upload Backup File</h3>
        <form action="{{ route('backups.upload') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
            @csrf
            <div class="flex-1">
                <label for="backup_file" class="block text-sm font-medium text-gray-700 mb-1">Backup File (.zip)</label>
                <input type="file" name="backup_file" id="backup_file"
                       accept=".zip" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Upload
            </button>
        </form>
    </div>

    <!-- Cleanup Old Backups -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-3">Cleanup Old Backups</h3>
        <form action="{{ route('backups.cleanup') }}" method="POST" class="flex items-end space-x-4">
            @csrf
            <div>
                <label for="keep_days" class="block text-sm font-medium text-gray-700 mb-1">Keep backups for (days)</label>
                <input type="number" name="keep_days" id="keep_days"
                       value="30" min="1" max="365"
                       class="border border-gray-300 rounded-lg px-3 py-2 w-24">
            </div>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg"
                    onclick="return confirm('Are you sure you want to cleanup old backups?')">
                Cleanup
            </button>
        </form>
    </div>

    <!-- Backups List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Available Backups</h3>
        </div>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Filename
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($backups as $backup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $backup['filename'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $backup['created_at']->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $backup['location'] === 'local' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($backup['location']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($backup['location'] === 'local')
                                        <a href="{{ route('backups.download', $backup['filename']) }}"
                                           class="text-blue-600 hover:text-blue-900">Download</a>

                                        <a href="{{ route('backups.info', $backup['filename']) }}"
                                           class="text-green-600 hover:text-green-900">Info</a>

                                        <button onclick="restoreBackup('{{ addslashes($backup['path']) }}')"
                                                class="text-orange-600 hover:text-orange-900">Restore</button>

                                        <form action="{{ route('backups.destroy', $backup['filename']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this backup?')">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="restoreExternalBackup('{{ addslashes($backup['path']) }}')"
                                                class="text-orange-600 hover:text-orange-900">Restore</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                No backups found. <a href="{{ route('backups.create') }}" class="text-blue-600 hover:text-blue-800">Create your first backup</a>
            </div>
        @endif
    </div>
</div>

<!-- Restore Modal -->
<div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 m-4 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Restore Database</h3>
        <p class="text-gray-600 mb-4">This will replace your current database with the backup. A backup of the current database will be created automatically.</p>

        <form id="restoreForm" method="POST">
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
    // Fix path separators for Windows
    const fixedPath = path.replace(/\\\\/g, '\\');
    document.getElementById('backup_path').value = fixedPath;
    document.getElementById('restoreForm').action = '{{ route("backups.restore") }}';
    document.getElementById('restoreModal').classList.remove('hidden');
    document.getElementById('restoreModal').classList.add('flex');
}

function restoreExternalBackup(path) {
    // Fix path separators for Windows
    const fixedPath = path.replace(/\\\\/g, '\\');
    document.getElementById('backup_path').value = fixedPath;
    document.getElementById('restoreForm').action = '{{ route("backups.restore-external") }}';
    document.getElementById('restoreForm').querySelector('input[name="backup_path"]').name = 'external_backup_path';
    document.getElementById('restoreModal').classList.remove('hidden');
    document.getElementById('restoreModal').classList.add('flex');
}

function closeRestoreModal() {
    document.getElementById('restoreModal').classList.add('hidden');
    document.getElementById('restoreModal').classList.remove('flex');
}
</script>
@endsection