<x-guest-layout>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <a href="{{ route('food.menu') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Order {{ $food->name }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Food Details -->
            <div>
                @if ($food->image_url)
                    <img src="{{ $food->image_url }}" alt="{{ $food->name }}" class="w-full h-64 object-cover rounded-lg mb-4">
                @else
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg mb-4">
                        <i class="fas fa-utensils text-gray-400 text-4xl"></i>
                    </div>
                @endif
                
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $food->name }}</h2>
                <p class="text-gray-600 mb-4">{{ $food->description }}</p>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-700">Price per item:</span>
                        <span class="text-xl font-bold text-blue-600">${{ number_format($food->price, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-700">Category:</span>
                        <span class="text-gray-800 capitalize">{{ $food->category }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Preparation time:</span>
                        <span class="text-gray-800">{{ $food->preparation_time }} minutes</span>
                    </div>
                </div>
            </div>

            <!-- Order Form -->
            <div>
                <form action="{{ route('food.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="food_id" value="{{ $food->id }}">

                    <!-- Guest Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Information</h3>
                        
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="guest_name" name="guest_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('guest_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="guest_email" name="guest_email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('guest_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label for="guest_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" id="guest_phone" name="guest_phone" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('guest_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number (Optional)</label>
                            <input type="text" id="room_number" name="room_number"
                                placeholder="e.g., 101, A205"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('room_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Type</h3>
                        
                        <div class="space-y-3 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="order_type" value="room_service" checked
                                    class="mr-2" onchange="toggleOrderType()">
                                <span>
                                    <i class="fas fa-bed mr-2"></i>Room Service
                                    <span class="text-sm text-gray-600 ml-2">(Delivered to your room)</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="order_type" value="dine_in"
                                    class="mr-2" onchange="toggleOrderType()">
                                <span>
                                    <i class="fas fa-utensils mr-2"></i>Dine-in
                                    <span class="text-sm text-gray-600 ml-2">(Eat at restaurant)</span>
                                </span>
                            </label>
                        </div>

                        <!-- Room Service Fields -->
                        <div id="room-service-fields" class="space-y-3">
                            <div>
                                <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number *</label>
                                <input type="text" id="room_number" name="room_number" required
                                    placeholder="e.g., 101, A205"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('room_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Dine-in Fields -->
                        <div id="dine-in-fields" class="space-y-3" style="display: none;">
                            <div>
                                <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Table Number *</label>
                                <input type="text" id="table_number" name="table_number"
                                    placeholder="e.g., T1, T12"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('table_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                            <textarea id="special_instructions" name="special_instructions" rows="3"
                                placeholder="Any special requests or dietary requirements..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('special_instructions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Price Display -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-gray-800">Total Price:</span>
                            <span class="text-2xl font-bold text-blue-600" id="total-price">${{ number_format($food->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('quantity').addEventListener('input', function() {
            const quantity = parseInt(this.value) || 1;
            const price = {{ $food->price }};
            const total = price * quantity;
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);
        });

        function toggleOrderType() {
            const orderType = document.querySelector('input[name="order_type"]:checked').value;
            const roomServiceFields = document.getElementById('room-service-fields');
            const dineInFields = document.getElementById('dine-in-fields');
            const roomNumberInput = document.getElementById('room_number');
            const tableNumberInput = document.getElementById('table_number');

            if (orderType === 'room_service') {
                roomServiceFields.style.display = 'block';
                dineInFields.style.display = 'none';
                roomNumberInput.required = true;
                tableNumberInput.required = false;
            } else {
                roomServiceFields.style.display = 'none';
                dineInFields.style.display = 'block';
                roomNumberInput.required = false;
                tableNumberInput.required = true;
            }
        }
    </script>
</x-guest-layout>
