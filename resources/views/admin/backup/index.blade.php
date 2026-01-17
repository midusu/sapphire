@extends('admin.layout')

@section('title', 'Backup & Restore - Sapphire Hotel Management')
@section('header', 'Backup & Restore')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Database Backup & Restore</h2>
    <p class="text-gray-600">Create and restore database backups</p>
</div>

<!-- Create Backup -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Create New Backup</h3>
            <p class="text-sm text-gray-600">Create a full database backup</p>
        </div>
        <form method="POST" action="{{ route('admin.security.backup.create') }}">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-database mr-2"></i>Create Backup
            </button>
        </form>
    </div>
</div>

<!-- Restore Backup -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Restore from Backup</h3>
    <form method="POST" action="{{ route('admin.security.backup.restore') }}" 
          onsubmit="return confirm('Are you sure you want to restore this backup? This will overwrite all current data!');">
        @csrf
        <div class="flex gap-4">
            <select name="backup_file" required class="flex-1 border-gray-300 rounded-lg">
                <option value="">Select a backup file...</option>
                @foreach($backups as $backup)
                    <option value="{{ $backup['filename'] }}">
                        {{ $backup['filename'] }} ({{ $backup['created_at']->format('M d, Y H:i') }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-undo mr-2"></i>Restore
            </button>
        </div>
    </form>
</div>

<!-- Backup Files List -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Available Backups</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filename</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($backups as $backup)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $backup['filename'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($backup['size'] / 1024, 2) }} KB
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $backup['created_at']->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <a href="{{ route('admin.security.backup.download', $backup['filename']) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <form method="POST" action="{{ route('admin.security.backup.destroy', $backup['filename']) }}" 
                                  class="inline" onsubmit="return confirm('Delete this backup?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No backups found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
