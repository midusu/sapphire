@extends('layouts.public')

@section('title', 'Order Food - Sapphire Hotel')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Food Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
            @if($food->image_url)
                <div class="md:w-1/3">
                    <img src="{{ $food->image_url }}" alt="{{ $food->name }}" class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif
            
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $food->name }}</h1>
                <p class="text-gray-600 mb-4">{{ $food->description }}</p>
                
                <div class="flex flex-wrap gap-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-tag text-green-600 mr-2"></i>
                        <span class="text-2xl font-bold text-green-600">${{ number_format($food->price, 2) }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        <span class="text-gray-700">{{ $food->preparation_time }} minutes</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-utensils text-purple-600 mr-2"></i>
                        <span class="text-gray-700">{{ $food->getMenuTypeLabel() }}</span>
                    </div>
                </div>
                
                @if($currentBooking)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-bed mr-2"></i>
                            Room Service to: {{ $currentBooking->room->room_number }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Form -->
    <form action="{{ route('guest.store-order') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        
        <input type="hidden" name="food_id" value="{{ $food->id }}">
        <input type="hidden" name="menu_type" value="{{ $food->menu_type }}">
        
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Order Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Type *</label>
                <div class="space-y-2">
                    @if($currentBooking)
                        <label class="flex items-center">
                            <input type="radio" name="order_type" value="room_service" checked
                                class="mr-2" onchange="toggleOrderType()">
                            <span>
                                <i class="fas fa-bed mr-2"></i>Room Service
                                <span class="text-sm text-gray-600 ml-2">(Delivered to Room {{ $currentBooking->room->room_number }})</span>
                            </span>
                        </label>
                    @endif
                    <label class="flex items-center">
                        <input type="radio" name="order_type" value="dine_in" 
                            @if(!$currentBooking) checked @endif
                            class="mr-2" onchange="toggleOrderType()">
                        <span>
                            <i class="fas fa-utensils mr-2"></i>Dine-in
                            <span class="text-sm text-gray-600 ml-2">(Eat at restaurant)</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="updateTotal()">
                @error('quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Table Number (for dine-in) -->
            <div id="table-number-field" @if($currentBooking) style="display: none;" @endif>
                <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">Table Number *</label>
                <input type="text" id="table_number" name="table_number" 
                    @if(!$currentBooking) required @endif
                    placeholder="e.g., T1, T12"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('table_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Scheduled Time -->
            <div>
                <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1"></i>Schedule Order (Optional)
                </label>
                <input type="datetime-local" id="scheduled_time" name="scheduled_time" 
                    min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for immediate ordering</p>
                @error('scheduled_time')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Special Instructions -->
        <div class="mt-6">
            <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                Special Instructions (Optional)
            </label>
            <textarea id="special_instructions" name="special_instructions" rows="3"
                placeholder="Any special requests or dietary requirements..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            @error('special_instructions')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Order Summary -->
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-900">Order Total</h3>
                    <p class="text-sm text-gray-600">{{ $food->name }} × <span id="quantity-display">1</span></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-green-600" id="total-price">${{ number_format($food->price, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-shopping-cart mr-2"></i>Place Order
            </button>
        </div>
    </form>
</div>

<script>
function toggleOrderType() {
    const orderType = document.querySelector('input[name="order_type"]:checked').value;
    const tableNumberField = document.getElementById('table-number-field');
    const tableNumberInput = document.getElementById('table_number');

    if (orderType === 'dine_in') {
        tableNumberField.style.display = 'block';
        tableNumberInput.required = true;
    } else {
        tableNumberField.style.display = 'none';
        tableNumberInput.required = false;
    }
}

function updateTotal() {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const price = {{ $food->price }};
    const total = price * quantity;
    
    document.getElementById('total-price').textContent = '$' + total.toFixed(2);
    document.getElementById('quantity-display').textContent = quantity;
}

// Initialize
toggleOrderType();
</script>
@endsection
