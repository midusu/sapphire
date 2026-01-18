@extends('admin.layout')

@section('title', 'New Booking - Sapphire Hotel Management')
@section('header', 'Create New Booking')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('admin.bookings.store') }}">
        @csrf
        
        <!-- Guest Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Information</h3>
            
            <!-- Guest Type Selection -->
            <div class="flex space-x-6 mb-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="guest_type" value="existing" class="form-radio text-blue-600 h-4 w-4" checked onchange="toggleGuestForm()">
                    <span class="ml-2 text-gray-700">Existing Guest</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="guest_type" value="new" class="form-radio text-blue-600 h-4 w-4" onchange="toggleGuestForm()">
                    <span class="ml-2 text-gray-700">New Guest</span>
                </label>
            </div>

            <div id="existingGuestSection">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Guest</label>
                <select name="user_id" id="user_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Select Guest</option>
                    @foreach(\App\Models\User::whereHas('role', function($q) { $q->where('slug', 'guest'); })->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- New Guest Form (Hidden by default) -->
            <div id="newGuestForm" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-800 mb-3">New Guest Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="new_guest[name]" id="new_guest_name" class="w-full border rounded-lg px-3 py-2">
                        @error('new_guest.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="new_guest[email]" id="new_guest_email" class="w-full border rounded-lg px-3 py-2">
                        @error('new_guest.email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="new_guest[phone]" id="new_guest_phone" class="w-full border rounded-lg px-3 py-2">
                        @error('new_guest.phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="new_guest[address]" id="new_guest_address" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Selection -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Selection</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Room Type</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" data-price="{{ $type->base_price }}">
                                {{ $type->name }} - ${{ number_format($type->base_price, 2) }}/night
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                    <input type="date" name="check_in_date" id="check_in_date" class="w-full border rounded-lg px-3 py-2" required min="{{ date('Y-m-d') }}">
                    @error('check_in_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                    <input type="date" name="check_out_date" id="check_out_date" class="w-full border rounded-lg px-3 py-2" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('check_out_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <!-- Hidden input for selected room -->
            <input type="hidden" name="room_id" id="room_id_input" required>

            <!-- Available Rooms (Loaded via AJAX) -->
            <div id="availableRooms" class="mt-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Room</label>
                <div id="roomsList" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Rooms will be loaded here -->
                </div>
            </div>
            
            <!-- Price Calculation -->
            <div id="priceCalculation" class="mt-4 p-4 bg-blue-50 rounded-lg hidden">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Amount:</span>
                    <span class="text-2xl font-bold text-blue-600">$<span id="totalAmount">0.00</span></span>
                </div>
            </div>
        </div>

        <!-- Guest Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Adults</label>
                    <input type="number" name="adults" min="1" value="1" class="w-full border rounded-lg px-3 py-2" required>
                    @error('adults')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Children</label>
                    <input type="number" name="children" min="0" value="0" class="w-full border rounded-lg px-3 py-2">
                    @error('children')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Special Requests -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Requests</h3>
            <textarea name="special_requests" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Any special requests or notes..."></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.bookings.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Create Booking
            </button>
        </div>
    </form>
</div>

<script>
function toggleGuestForm() {
    const isNew = document.querySelector('input[name="guest_type"][value="new"]').checked;
    const existingSection = document.getElementById('existingGuestSection');
    const newSection = document.getElementById('newGuestForm');
    const userSelect = document.getElementById('user_id');
    const newInputs = newSection.querySelectorAll('input');

    if (isNew) {
        existingSection.classList.add('hidden');
        newSection.classList.remove('hidden');
        
        // Disable validation for existing user
        userSelect.required = false;
        userSelect.value = '';
        
        // Enable validation for new guest fields
        document.getElementById('new_guest_name').required = true;
        document.getElementById('new_guest_email').required = true;
        document.getElementById('new_guest_phone').required = true;
    } else {
        existingSection.classList.remove('hidden');
        newSection.classList.add('hidden');
        
        // Enable validation for existing user
        userSelect.required = true;
        
        // Disable validation for new guest fields
        newInputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
    }
}

// Initialize on page load (in case of validation error redirect)
document.addEventListener('DOMContentLoaded', function() {
    toggleGuestForm();
});

// Load available rooms when dates and room type are selected
document.getElementById('check_in_date').addEventListener('change', loadAvailableRooms);
document.getElementById('check_out_date').addEventListener('change', loadAvailableRooms);
document.getElementById('room_type_id').addEventListener('change', loadAvailableRooms);

function loadAvailableRooms() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const roomTypeId = document.getElementById('room_type_id').value;
    
    if (checkIn && checkOut && roomTypeId) {
        // Validate dates
        if (new Date(checkIn) >= new Date(checkOut)) {
            alert('Check-out date must be after check-in date');
            document.getElementById('check_out_date').value = '';
            return;
        }

        fetch(`/admin/bookings/available-rooms?check_in_date=${checkIn}&check_out_date=${checkOut}&room_type_id=${roomTypeId}`)
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.error || 'Network response was not ok');
                }
                return response.json();
            })
            .then(rooms => {
                const roomsList = document.getElementById('roomsList');
                const availableRoomsDiv = document.getElementById('availableRooms');
                
                if (rooms.length > 0) {
                    roomsList.innerHTML = rooms.map(room => {
                        const roomTypeName = room.room_type ? room.room_type.name : 'Unknown Type';
                        const roomPrice = room.room_type ? room.room_type.base_price : 0;
                        
                        return `
                        <div class="border rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition" 
                             onclick="selectRoom(this, ${room.id}, '${room.room_number}', ${roomPrice})">
                            <div class="font-medium">Room ${room.room_number}</div>
                            <div class="text-sm text-gray-600">${roomTypeName}</div>
                            <div class="text-sm text-green-600 font-medium">Available</div>
                        </div>
                    `}).join('');
                    availableRoomsDiv.classList.remove('hidden');
                } else {
                    roomsList.innerHTML = '<div class="col-span-3 text-center text-red-500 py-4 font-medium">No rooms available for selected dates</div>';
                    availableRoomsDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to load available rooms. Please try again.');
            });
    }
}

function selectRoom(element, roomId, roomNumber, price) {
    // Remove previous selections
    document.querySelectorAll('#roomsList > div').forEach(div => {
        div.classList.remove('bg-blue-100', 'border-blue-500', 'ring-2', 'ring-blue-500');
    });
    
    // Add selection to clicked room
    element.classList.add('bg-blue-100', 'border-blue-500', 'ring-2', 'ring-blue-500');
    
    // Store selected room
    document.getElementById('room_id_input').value = roomId;
    document.selectedRoomId = roomId;
    
    // Calculate price
    calculateTotal(price);
}

function calculateTotal(pricePerNight) {
    const checkIn = new Date(document.getElementById('check_in_date').value);
    const checkOut = new Date(document.getElementById('check_out_date').value);
    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    const total = pricePerNight * nights;
    
    document.getElementById('totalAmount').textContent = total.toFixed(2);
    document.getElementById('priceCalculation').classList.remove('hidden');
}

</script>
@endsection
