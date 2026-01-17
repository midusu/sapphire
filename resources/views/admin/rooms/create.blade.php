@extends('admin.layout')

@section('title', 'Add Room - Sapphire Hotel Management')
@section('header', 'Add New Room')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.rooms.store') }}">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <input type="text" name="room_number" class="w-full border rounded-lg px-3 py-2" 
                           placeholder="e.g., 101, A205" required>
                    @error('room_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                    <input type="number" name="floor" class="w-full border rounded-lg px-3 py-2" 
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
                            <option value="{{ $roomType->id }}">{{ $roomType->name }} - ${{ number_format($roomType->base_price, 2) }}/night</option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="cleaning">Cleaning</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    @error('status')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Room Type Preview -->
        <div id="roomTypePreview" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Type Details</h3>
            <div id="roomTypeInfo" class="space-y-3">
                <!-- Room type info will be populated here -->
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h3>
            <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2" 
                      placeholder="Any special notes about this room (e.g., view, special features, maintenance history)..."></textarea>
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
                <i class="fas fa-save mr-2"></i>Create Room
            </button>
        </div>
    </form>
</div>

<script>
// Load room type details when selected
document.querySelector('select[name="room_type_id"]').addEventListener('change', function() {
    const roomTypeId = this.value;
    const preview = document.getElementById('roomTypePreview');
    const info = document.getElementById('roomTypeInfo');
    
    if (roomTypeId) {
        // Get room type data (you can pass this via JSON in the view or fetch via API)
        const roomTypes = @json($roomTypes);
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
                    <div>
                        <span class="text-sm text-gray-600">Status:</span>
                        <p class="font-medium">${selectedType.is_active ? 'Active' : 'Inactive'}</p>
                    </div>
                </div>
                ${selectedType.description ? `
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">Description:</span>
                        <p class="text-gray-700">${selectedType.description}</p>
                    </div>
                ` : ''}
                ${selectedType.amenities ? `
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">Amenities:</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            ${JSON.parse(selectedType.amenities).map(amenity => 
                                `<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">${amenity}</span>`
                            ).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
            preview.classList.remove('hidden');
        }
    } else {
        preview.classList.add('hidden');
    }
});

// Auto-generate room number based on floor
document.querySelector('input[name="floor"]').addEventListener('input', function() {
    const floor = this.value;
    const roomNumberField = document.querySelector('input[name="room_number"]');
    
    if (floor && !roomNumberField.value) {
        // Get existing rooms on this floor to suggest next number
        fetch(`/api/rooms/next-number?floor=${floor}`)
            .then(response => response.json())
            .then(data => {
                if (data.next_number) {
                    roomNumberField.value = data.next_number;
                }
            })
            .catch(error => {
                // Fallback: just use floor + 01
                roomNumberField.value = floor + '01';
            });
    }
});
</script>
@endsection
