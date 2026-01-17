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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guest Email</label>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Or Register New Guest</label>
                    <button type="button" onclick="showNewGuestForm()" class="w-full bg-gray-100 border rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-200 transition">
                        <i class="fas fa-user-plus mr-2"></i>Register New Guest
                    </button>
                </div>
            </div>
            
            <!-- New Guest Form (Hidden by default) -->
            <div id="newGuestForm" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-3">New Guest Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="new_guest[name]" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="new_guest[email]" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="new_guest[phone]" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="new_guest[address]" class="w-full border rounded-lg px-3 py-2">
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
                    <input type="date" name="check_in_date" id="check_in_date" class="w-full border rounded-lg px-3 py-2" required>
                    @error('check_in_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                    <input type="date" name="check_out_date" id="check_out_date" class="w-full border rounded-lg px-3 py-2" required>
                    @error('check_out_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
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
function showNewGuestForm() {
    document.getElementById('newGuestForm').classList.remove('hidden');
    document.getElementById('user_id').value = '';
}

// Load available rooms when dates and room type are selected
document.getElementById('check_in_date').addEventListener('change', loadAvailableRooms);
document.getElementById('check_out_date').addEventListener('change', loadAvailableRooms);
document.getElementById('room_type_id').addEventListener('change', loadAvailableRooms);

function loadAvailableRooms() {
    const checkIn = document.getElementById('check_in_date').value;
    const checkOut = document.getElementById('check_out_date').value;
    const roomTypeId = document.getElementById('room_type_id').value;
    
    if (checkIn && checkOut && roomTypeId) {
        fetch(`/admin/bookings/available-rooms?check_in_date=${checkIn}&check_out_date=${checkOut}&room_type_id=${roomTypeId}`)
            .then(response => response.json())
            .then(rooms => {
                const roomsList = document.getElementById('roomsList');
                const availableRoomsDiv = document.getElementById('availableRooms');
                
                if (rooms.length > 0) {
                    roomsList.innerHTML = rooms.map(room => `
                        <div class="border rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition" onclick="selectRoom(${room.id}, '${room.room_number}', ${room.room_type.base_price})">
                            <div class="font-medium">${room.room_number}</div>
                            <div class="text-sm text-gray-600">${room.room_type.name}</div>
                            <div class="text-sm text-green-600">Available</div>
                        </div>
                    `).join('');
                    availableRoomsDiv.classList.remove('hidden');
                } else {
                    roomsList.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-4">No rooms available for selected dates</div>';
                    availableRoomsDiv.classList.remove('hidden');
                }
            });
    }
}

function selectRoom(roomId, roomNumber, price) {
    // Remove previous selections
    document.querySelectorAll('#roomsList > div').forEach(div => {
        div.classList.remove('bg-blue-50', 'border-blue-500');
    });
    
    // Add selection to clicked room
    event.target.closest('div').classList.add('bg-blue-50', 'border-blue-500');
    
    // Store selected room
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

// Add hidden input for room_id when form is submitted
document.querySelector('form').addEventListener('submit', function(e) {
    if (document.selectedRoomId) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'room_id';
        hiddenInput.value = document.selectedRoomId;
        this.appendChild(hiddenInput);
    } else {
        e.preventDefault();
        alert('Please select a room');
    }
});
</script>
@endsection
