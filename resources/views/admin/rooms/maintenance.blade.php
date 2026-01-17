@extends('admin.layout')

@section('title', 'Maintenance - Sapphire Hotel Management')
@section('header', 'Room Maintenance')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Room Maintenance</h2>
        <p class="text-gray-600">Track and manage room maintenance tasks</p>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.rooms.housekeeping') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-broom mr-2"></i>Housekeeping
        </a>
        <a href="{{ route('admin.rooms.floor-plan') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-map mr-2"></i>Floor Plan
        </a>
        <a href="{{ route('admin.rooms.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
        </a>
    </div>
</div>

<!-- Maintenance Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Under Maintenance</p>
                <p class="text-2xl font-bold text-orange-600">{{ $rooms->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-tools text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Rooms</p>
                <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Room::count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-door-open text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Availability Impact</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ \App\Models\Room::count() > 0 ? round(($rooms->count() / \App\Models\Room::count()) * 100) : 0 }}%
                </p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Rooms -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Rooms Under Maintenance</h3>
    </div>
    
    @forelse($rooms as $room)
        <div class="p-6 border-b hover:bg-gray-50 transition">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Room Information -->
                <div>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">{{ $room->room_number }}</h4>
                            <p class="text-sm text-gray-600">{{ $room->roomType->name }} - Floor {{ $room->floor }}</p>
                        </div>
                        <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                            Under Maintenance
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Type:</span>
                            <span class="font-medium">{{ $room->roomType->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Rate:</span>
                            <span class="font-medium">${{ number_format($room->roomType->base_price, 2) }}/night</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Max Occupancy:</span>
                            <span class="font-medium">{{ $room->roomType->max_occupancy }} guests</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status Since:</span>
                            <span class="font-medium">{{ $room->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Maintenance Details -->
                <div>
                    <h5 class="font-medium text-gray-800 mb-3">Maintenance Information</h5>
                    <div class="space-y-3">
                        @if($room->notes)
                            <div>
                                <span class="text-sm text-gray-600">Notes:</span>
                                <p class="text-sm text-gray-700 mt-1">{{ $room->notes }}</p>
                            </div>
                        @else
                            <div class="text-sm text-gray-500 italic">
                                No maintenance notes provided
                            </div>
                        @endif
                        
                        <!-- Recent Maintenance History (if any) -->
                        <div>
                            <span class="text-sm text-gray-600">Recent Activity:</span>
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-tools text-orange-500 mr-2"></i>
                                    Room marked for maintenance {{ $room->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div>
                    <h5 class="font-medium text-gray-800 mb-3">Actions</h5>
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}">
                            @csrf
                            <div class="flex space-x-2">
                                <button type="submit" name="status" value="available" 
                                        class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition"
                                        onclick="return confirm('Mark this room as available and ready for booking?')">
                                    <i class="fas fa-check mr-2"></i>Mark Available
                                </button>
                                <button type="submit" name="status" value="cleaning" 
                                        class="flex-1 bg-yellow-600 text-white px-3 py-2 rounded text-sm hover:bg-yellow-700 transition">
                                    <i class="fas fa-broom mr-2"></i>Needs Cleaning
                                </button>
                            </div>
                        </form>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}" 
                               class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition text-center">
                                <i class="fas fa-edit mr-2"></i>Edit Room
                            </a>
                            <a href="{{ route('admin.rooms.show', $room) }}" 
                               class="flex-1 bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700 transition text-center">
                                <i class="fas fa-eye mr-2"></i>Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="p-12 text-center text-gray-500">
            <i class="fas fa-tools text-4xl mb-4"></i>
            <p class="text-lg">No rooms under maintenance</p>
            <p class="text-sm mt-2">All rooms are available or occupied</p>
        </div>
    @endforelse
</div>

<!-- Maintenance Schedule -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Maintenance Guidelines</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Routine Maintenance</h4>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>HVAC system inspection - Monthly</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Plumbing fixtures check - Quarterly</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Electrical systems inspection - Bi-annually</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span>Paint and touch-ups - Annually</span>
                </li>
            </ul>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-700 mb-3">Emergency Procedures</h4>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2 mt-0.5"></i>
                    <span>Report urgent issues immediately to maintenance team</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2 mt-0.5"></i>
                    <span>Document all maintenance activities in room notes</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2 mt-0.5"></i>
                    <span>Update room status before and after maintenance</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-orange-500 mr-2 mt-0.5"></i>
                    <span>Coordinate with housekeeping for post-maintenance cleaning</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <a href="{{ route('admin.rooms.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i>Add New Room
    </a>
    <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
        <i class="fas fa-print mr-2"></i>Print Report
    </button>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
