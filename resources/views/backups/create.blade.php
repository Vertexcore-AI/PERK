@extends('layouts.app')

@section('title', 'Create Database Backup')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create Database Backup</h1>
            <p class="text-gray-600 mt-2">
                Create a backup of your SQLite database. The backup will include all data and can be stored locally or copied to an external location.
            </p>
        </div>

        <form action="{{ route('backups.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="external_path" class="block text-sm font-medium text-gray-700 mb-2">
                    External Backup Path (Optional)
                </label>
                <input type="text" name="external_path" id="external_path"
                       placeholder="e.g., D:\Backups or \\server\backups"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">
                    If specified, the backup will also be copied to this external location.
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">Backup Contents</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Complete SQLite database file</li>
                    <li>• All tables and data</li>
                    <li>• Backup metadata and information</li>
                    <li>• Compressed ZIP format for easy storage</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-yellow-800 mb-2">Important Notes</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Backup process may take a few moments depending on database size</li>
                    <li>• Ensure external path is accessible and writable</li>
                    <li>• Regular backups are recommended for data safety</li>
                </ul>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('backups.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Create Backup
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Backups -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Backups</h2>
        <div class="text-sm text-gray-600">
            <p>View and manage your existing backups on the <a href="{{ route('backups.index') }}" class="text-blue-600 hover:text-blue-800">backups page</a>.</p>
        </div>
    </div>
</div>
@endsection