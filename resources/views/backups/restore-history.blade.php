@extends('layouts.app')

@section('title', 'Restore History')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Restore History</h1>
        <a href="{{ route('backups.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Backups</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Recent Restore Operations</h3>
            <p class="text-sm text-gray-600 mt-1">History of database restore operations performed on this system</p>
        </div>

        @if(count($history) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Backup File
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Message
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($history as $entry)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($entry['timestamp'])->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <span class="font-mono">{{ $entry['backup_file'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $entry['success'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $entry['success'] ? 'Success' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $entry['message'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                <div class="mb-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No restore history</h3>
                <p class="text-gray-600">No database restore operations have been performed yet.</p>
                <a href="{{ route('backups.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    View available backups
                </a>
            </div>
        @endif
    </div>

    <!-- Statistics -->
    @if(count($history) > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ count($history) }}</div>
                    <div class="text-sm text-blue-700">Total Restores</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">
                        {{ count(array_filter($history, function($entry) { return $entry['success']; })) }}
                    </div>
                    <div class="text-sm text-green-700">Successful</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600">
                        {{ count(array_filter($history, function($entry) { return !$entry['success']; })) }}
                    </div>
                    <div class="text-sm text-red-700">Failed</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">About Restore History</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• History shows the last 50 restore operations</li>
            <li>• Each restore automatically creates a backup of the current database</li>
            <li>• Failed restores do not modify the existing database</li>
            <li>• Successful restores replace the entire database with the backup content</li>
        </ul>
    </div>
</div>
@endsection