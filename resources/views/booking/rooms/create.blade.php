@extends('layouts.public')

@section('title', 'Create Room Booking - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Complete Your Booking</h1>
            <p class="text-xl text-gray-600">Fill in the details to reserve your room</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 hidden" id="room_image_container">
            <div class="h-64 relative">
                <img id="room_image" src="" alt="Room Preview" class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 bg-gradient-to-t from-black/60 to-transparent p-4 w-full">
                    <h3 id="room_image_title" class="text-white text-2xl font-bold"></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('booking.rooms.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                        <select name="room_type_id" id="room_type" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="">Select Room Type</option>
                            @foreach($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" 
                                    data-price="{{ $roomType->base_price }}"
                                    {{ request('room_type') == $roomType->id ? 'selected' : '' }}>
                                    {{ $roomType->name }} - ${{ number_format($roomType->base_price, 2) }}/night
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="room" class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                        <select name="room_id" id="room" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="">Select Room Type First</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                        <input type="date" name="check_in_date" id="check_in_date" 
                               class="w-full border rounded-lg px-3 py-2" required
                               min="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div>
                        <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                        <input type="date" name="check_out_date" id="check_out_date" 
                               class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="adults" class="block text-sm font-medium text-gray-700 mb-2">Adults</label>
                        <input type="number" name="adults" id="adults" 
                               class="w-full border rounded-lg px-3 py-2" min="1" value="1" required>
                    </div>

                    <div>
                        <label for="children" class="block text-sm font-medium text-gray-700 mb-2">Children</label>
                        <input type="number" name="children" id="children" 
                               class="w-full border rounded-lg px-3 py-2" min="0" value="0">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="peer sr-only" required>
                            <div class="rounded-lg border-2 peer-checked:border-blue-600 peer-checked:bg-blue-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-credit-card text-2xl mb-2 text-gray-600 peer-checked:text-blue-600"></i>
                                <div class="text-sm font-medium">Card</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-blue-600 peer-checked:bg-blue-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-money-bill text-2xl mb-2 text-gray-600 peer-checked:text-blue-600"></i>
                                <div class="text-sm font-medium">Cash</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="online" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-blue-600 peer-checked:bg-blue-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-globe text-2xl mb-2 text-gray-600 peer-checked:text-blue-600"></i>
                                <div class="text-sm font-medium">Online</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-blue-600 peer-checked:bg-blue-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-university text-2xl mb-2 text-gray-600 peer-checked:text-blue-600"></i>
                                <div class="text-sm font-medium">Bank Transfer</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Guest Information (only shown if not logged in) -->
                @guest
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Guest Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="guest_name" id="guest_name" 
                                   class="w-full border rounded-lg px-3 py-2" required>
                        </div>

                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="guest_email" id="guest_email" 
                                   class="w-full border rounded-lg px-3 py-2" required>
                        </div>

                        <div>
                            <label for="guest_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="guest_phone" id="guest_phone" 
                                   class="w-full border rounded-lg px-3 py-2" required>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Your booking details will be sent to this email address for confirmation.
                    </p>
                </div>
                @else
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Booking as: {{ auth()->user()->name }} ({{ auth()->user()->email }})
                    </p>
                </div>
                @endguest

                <div class="mb-6">
                    <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">Special Requests (Optional)</label>
                    <textarea name="special_requests" id="special_requests" rows="4" 
                              class="w-full border rounded-lg px-3 py-2" 
                              placeholder="Any special requests or requirements..."></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Price per night:</span>
                        <span id="price_per_night" class="font-semibold">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Number of nights:</span>
                        <span id="number_of_nights" class="font-semibold">0</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold">Total Amount:</span>
                            <span id="total_amount" class="text-2xl font-bold text-blue-600">$0.00</span>
                        </div>

                        <!-- Coupon Section -->
                        <div class="mb-4 pt-4 border-t border-dashed">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Have a coupon?</label>
                            <div class="flex gap-2">
                                <input type="text" id="coupon_code_input" name="coupon_code" class="flex-1 border rounded-lg px-3 py-2 text-sm uppercase" placeholder="Enter code">
                                <button type="button" id="apply_coupon_btn" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">Apply</button>
                            </div>
                            <div id="coupon_message" class="mt-2 text-sm hidden"></div>
                            <div id="discount_row" class="flex justify-between items-center mt-2 text-green-600 hidden">
                                <span>Discount applied:</span>
                                <span id="discount_amount" class="font-semibold">-$0.00</span>
                            </div>
                        </div>
                        
                        @if(config('hotel.booking.require_deposit'))
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <h4 class="font-semibold text-blue-900 mb-2">Payment Options</h4>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="payment_type" value="full" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-900">Full Payment</span>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="payment_type" value="deposit" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-900">
                                        Pay Deposit Only ({{ config('hotel.booking.deposit_percentage') }}%)
                                        <span id="deposit_amount_display" class="font-bold text-blue-700 ml-1"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="payment_type" value="full">
                        @endif
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Complete Booking
                    </button>
                    <a href="{{ route('booking.rooms.index') }}" 
                       class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeSelect = document.getElementById('room_type');
    const roomSelect = document.getElementById('room');
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    const pricePerNight = document.getElementById('price_per_night');
    const numberOfNights = document.getElementById('number_of_nights');
    const totalAmount = document.getElementById('total_amount');
    
    // Debug: Check if elements exist
    console.log('Elements found:');
    console.log('roomTypeSelect:', roomTypeSelect);
    console.log('roomSelect:', roomSelect);
    console.log('checkInDate:', checkInDate);
    console.log('checkOutDate:', checkOutDate);
    console.log('pricePerNight:', pricePerNight);
    console.log('numberOfNights:', numberOfNights);
    console.log('totalAmount:', totalAmount);

    // Room data
    const roomData = {!! $roomTypes->map(function($rt) {
        return [
            'id' => $rt->id,
            'name' => $rt->name,
            'image_url' => $rt->image_url,
            'price_per_night' => (float) $rt->base_price,
            'rooms' => $rt->rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'status' => $room->status
                ];
            })
        ];
    })->toJson() !!};
    
    console.log('Room data loaded:', roomData);

    // Handle room type change
    if (roomTypeSelect) {
        roomTypeSelect.addEventListener('change', function() {
            const selectedTypeId = this.value;
            console.log('Room type selected:', selectedTypeId);
            roomSelect.innerHTML = '<option value="">Select a room</option>';
            
            if (selectedTypeId) {
                const selectedType = roomData.find(rt => rt.id == selectedTypeId);
                console.log('Found room type:', selectedType);
                
                // Update image
                const imageContainer = document.getElementById('room_image_container');
                const roomImage = document.getElementById('room_image');
                const roomImageTitle = document.getElementById('room_image_title');
                
                if (selectedType && selectedType.image_url) {
                    roomImage.src = selectedType.image_url;
                    roomImageTitle.textContent = selectedType.name;
                    imageContainer.classList.remove('hidden');
                } else {
                    imageContainer.classList.add('hidden');
                }

                if (selectedType && selectedType.rooms.length > 0) {
                    console.log('Available rooms:', selectedType.rooms.length);
                    selectedType.rooms.forEach(room => {
                        if (room.status === 'available') {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `${room.room_number} - ${selectedType.name}`;
                            roomSelect.appendChild(option);
                            console.log('Added room:', room.room_number);
                        }
                    });
                } else {
                    console.log('No rooms available for this room type');
                }
            }
            updatePrice();
        });
        console.log('Event listener attached to room type select');
    } else {
        console.log('Room type select element not found!');
    }

    // Handle date changes
    checkInDate.addEventListener('change', updatePrice);
    checkOutDate.addEventListener('change', updatePrice);

    function updatePrice() {
        const selectedTypeId = roomTypeSelect.value;
        const selectedType = roomData.find(rt => rt.id == selectedTypeId);
        
        if (selectedType && checkInDate.value && checkOutDate.value) {
            const start = new Date(checkInDate.value);
            const end = new Date(checkOutDate.value);
            const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            
            if (nights > 0) {
                pricePerNight.textContent = `$${selectedType.price_per_night.toFixed(2)}`;
                numberOfNights.textContent = nights;
                totalAmount.textContent = `$${(selectedType.price_per_night * nights).toFixed(2)}`;
                
                // Update deposit amount display
                 const total = selectedType.price_per_night * nights;
                 const depositLines = document.getElementById('deposit_amount_display');
                 if(depositLines) {
                     const depositPercent = {{ config('hotel.booking.deposit_percentage', 20) }};
                     const deposit = total * (depositPercent / 100);
                     depositLines.textContent = `$${deposit.toFixed(2)}`;
                 }
            } else {
                pricePerNight.textContent = '$0.00';
                numberOfNights.textContent = '0';
                totalAmount.textContent = '$0.00';
            }
        } else {
            pricePerNight.textContent = '$0.00';
            numberOfNights.textContent = '0';
            totalAmount.textContent = '$0.00';
        }
    }

    // Set initial room type if provided
    @if(request('room_type'))
        roomTypeSelect.value = '{{ request('room_type') }}';
        roomTypeSelect.dispatchEvent(new Event('change'));
    @endif
});
</script>
@endsection
