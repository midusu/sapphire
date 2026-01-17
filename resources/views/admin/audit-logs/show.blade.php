@extends('admin.layout')

@section('title', 'Audit Log Details - Sapphire Hotel Management')
@section('header', 'Audit Log Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.security.audit-logs.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Back to Audit Logs
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="text-xl font-semibold text-gray-800">Audit Log #{{ $auditLog->id }}</h2>
    </div>
    
    <div class="p-6 space-y-6">
        <!-- Basic Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">User</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->user ? $auditLog->user->name . ' (' . $auditLog->user->email . ')' : 'System' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Action</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $auditLog->action_color }}-100 text-{{ $auditLog->action_color }}-800">
                            {{ ucfirst($auditLog->action) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">IP Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $auditLog->description }}</p>
        </div>

        <!-- Model Information -->
        @if($auditLog->model_type)
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Model Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Model Type</label>
                    <p class="mt-1 text-sm text-gray-900">{{ class_basename($auditLog->model_type) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Model ID</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->model_id }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Changes -->
        @if($auditLog->old_values || $auditLog->new_values)
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Changes</h3>
            <div class="grid grid-cols-2 gap-4">
                @if($auditLog->old_values)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Old Values</label>
                    <pre class="text-xs bg-red-50 p-3 rounded overflow-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
                @if($auditLog->new_values)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Values</label>
                    <pre class="text-xs bg-green-50 p-3 rounded overflow-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Request Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Request Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Route</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->route ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Method</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $auditLog->method ?? 'N/A' }}</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Agent</label>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $auditLog->user_agent ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Request Data -->
        @if($auditLog->request_data)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Request Data</label>
            <pre class="text-xs bg-gray-50 p-3 rounded overflow-auto">{{ json_encode($auditLog->request_data, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>
</div>
@endsection
