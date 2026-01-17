@extends('admin.layout')

@section('title', 'Edit Room - Sapphire Hotel Management')
@section('header', 'Edit Room: ' . $room->room_number)

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <input type="text" name="room_number" class="w-full border rounded-lg px-3 py-2" 
                           value="{{ old('room_number', $room->room_number) }}"
                           placeholder="e.g., 101, A205" required>
                    @error('room_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                    <input type="number" name="floor" class="w-full border rounded-lg px-3 py-2" 
                           value="{{ old('floor', $room->floor) }}"
                           min="1" max="20" placeholder="e.g., 1, 2, 3" required>
                    @error('floor')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                    <select name="room_type_id" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Room Type</option>
                        @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                {{ $roomType->name }} - ${{ number_format($roomType->base_price, 2) }}/night
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="cleaning" {{ old('status', $room->status) == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                        <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Room Type Preview -->
        <div id="roomTypePreview" class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Type Details</h3>
            <div id="roomTypeInfo" class="space-y-3">
                <!-- Room type info will be populated here -->
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h3>
            <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2" 
                      placeholder="Any special notes about this room...">{{ old('notes', $room->notes) }}</textarea>
            @error('notes')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.rooms.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Update Room
            </button>
        </div>
    </form>
</div>

<script>
// Load room type details when selected
const roomTypeSelect = document.querySelector('select[name="room_type_id"]');
const preview = document.getElementById('roomTypePreview');
const info = document.getElementById('roomTypeInfo');
const roomTypes = @json($roomTypes);

function updateRoomTypePreview() {
    const roomTypeId = roomTypeSelect.value;
    
    if (roomTypeId) {
        const selectedType = roomTypes.find(type => type.id == roomTypeId);
        
        if (selectedType) {
            info.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Room Type:</span>
                        <p class="font-medium">${selectedType.name}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Base Price:</span>
                        <p class="font-medium text-green-600">$${selectedType.base_price}/night</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Max Occupancy:</span>
                        <p class="font-medium">${selectedType.max_occupancy} guests</p>
                    </div>
                </div>
            `;
            preview.classList.remove('hidden');
        }
    } else {
        preview.classList.add('hidden');
    }
}

roomTypeSelect.addEventListener('change', updateRoomTypePreview);

// Initialize preview on page load
if (roomTypeSelect.value) {
    updateRoomTypePreview();
}
</script>
@endsection
