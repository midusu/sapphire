@extends('admin.layout')

@section('title', 'Record Payment - Sapphire Hotel Management')
@section('header', 'Record Payment')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf

            <!-- Payment Type Selection -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Type</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment_type" value="booking" class="mr-3" required>
                        <div>
                            <div class="font-medium">Room Booking Payment</div>
                            <div class="text-sm text-gray-600">Payment for hotel room reservations</div>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment_type" value="activity" class="mr-3" required>
                        <div>
                            <div class="font-medium">Activity Payment</div>
                            <div class="text-sm text-gray-600">Payment for activities (zipline, swimming, etc.)</div>
                        </div>
                    </label>
                </div>
                @error('payment_type')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Booking Selection (shown when booking is selected) -->
            <div id="bookingSelection" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Booking</h3>
                <select name="booking_id" id="booking_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Select a booking...</option>
                    @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}" data-amount="{{ $booking->total_amount }}"
                            data-guest="{{ $booking->user->name }}" data-room="{{ $booking->room->room_number }}"
                            data-dates="{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d') }}">
                            {{ $booking->user->name }} - Room {{ $booking->room->room_number }}
                            ({{ $booking->check_in_date->format('M d') }} to {{ $booking->check_out_date->format('M d') }}) -
                            ${{ number_format($booking->total_amount, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('booking_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Activity Selection (shown when activity is selected) -->
            <div id="activitySelection" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Activity Booking</h3>
                <select name="activity_booking_id" id="activity_booking_id" class="w-full border rounded-lg px-3 py-2"
                    required>
                    <option value="">Select an activity booking...</option>
                    @foreach($activityBookings as $activityBooking)
                        <option value="{{ $activityBooking->id }}" data-amount="{{ $activityBooking->total_price }}"
                            data-guest="{{ $activityBooking->user->name }}"
                            data-activity="{{ $activityBooking->activity->name }}"
                            data-time="{{ $activityBooking->scheduled_time->format('M d, H:i') }}">
                            {{ $activityBooking->user->name }} - {{ $activityBooking->activity->name }}
                            ({{ $activityBooking->scheduled_time->format('M d, H:i') }}) -
                            ${{ number_format($activityBooking->total_price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('activity_booking_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Details -->
            <div id="paymentDetails" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h3>

                <!-- Selected Item Summary -->
                <div id="selectedSummary" class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <!-- Summary will be populated by JavaScript -->
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="amount" id="amount" step="0.01" min="0"
                                class="w-full border rounded-lg pl-8 pr-3 py-2" required>
                        </div>
                        @error('amount')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="w-full border rounded-lg px-3 py-2"
                            required>
                            <option value="">Select method...</option>
                            <option value="cash">Cash</option>
                            <option value="card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online Payment</option>
                        </select>
                        @error('payment_method')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (Optional)</label>
                        <input type="text" name="transaction_id" class="w-full border rounded-lg px-3 py-2"
                            placeholder="Bank reference, receipt number, etc.">
                        @error('transaction_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code (Optional)</label>
                        <input type="text" name="coupon_code" class="w-full border rounded-lg px-3 py-2"
                            placeholder="Enter promo code">
                        @error('coupon_code')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Discount will be applied on submission.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="datetime-local" name="payment_date" class="w-full border rounded-lg px-3 py-2"
                            value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div id="notesSection" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h3>
                <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2"
                    placeholder="Any additional notes about this payment..."></textarea>
            </div>

            <!-- Submit Buttons -->
            <div id="submitSection" class="flex justify-end space-x-4 hidden">
                <a href="{{ route('admin.payments.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>

    <script>
        // Handle payment type selection
        document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const bookingSection = document.getElementById('bookingSelection');
                const activitySection = document.getElementById('activitySelection');

                if (this.value === 'booking') {
                    bookingSection.classList.remove('hidden');
                    activitySection.classList.add('hidden');
                } else {
                    bookingSection.classList.add('hidden');
                    activitySection.classList.remove('hidden');
                }
            });
        });

        // Handle booking selection
        document.getElementById('booking_id').addEventListener('change', function () {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                showPaymentDetails(option, 'booking');
            } else {
                hidePaymentDetails();
            }
        });

        // Handle activity booking selection
        document.getElementById('activity_booking_id').addEventListener('change', function () {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                showPaymentDetails(option, 'activity');
            } else {
                hidePaymentDetails();
            }
        });

        function showPaymentDetails(option, type) {
            const summary = document.getElementById('selectedSummary');
            const amount = document.getElementById('amount');

            if (type === 'booking') {
                summary.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Guest:</span>
                        <p class="font-medium">${option.dataset.guest}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Room:</span>
                        <p class="font-medium">${option.dataset.room}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Stay Period:</span>
                        <p class="font-medium">${option.dataset.dates}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Total Amount:</span>
                        <p class="font-medium text-green-600">$${option.dataset.amount}</p>
                    </div>
                </div>
            `;
            } else {
                summary.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600">Guest:</span>
                        <p class="font-medium">${option.dataset.guest}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Activity:</span>
                        <p class="font-medium">${option.dataset.activity}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Scheduled Time:</span>
                        <p class="font-medium">${option.dataset.time}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Total Amount:</span>
                        <p class="font-medium text-green-600">$${option.dataset.amount}</p>
                    </div>
                </div>
            `;
            }

            amount.value = option.dataset.amount;

            document.getElementById('paymentDetails').classList.remove('hidden');
            document.getElementById('notesSection').classList.remove('hidden');
            document.getElementById('submitSection').classList.remove('hidden');
        }

        function hidePaymentDetails() {
            document.getElementById('paymentDetails').classList.add('hidden');
            document.getElementById('notesSection').classList.add('hidden');
            document.getElementById('submitSection').classList.add('hidden');
        }
    </script>
@endsection