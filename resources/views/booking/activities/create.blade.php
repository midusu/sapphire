@extends('layouts.app')

@section('title', 'Create Activity Booking - Sapphire Hotel')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Complete Your Activity Booking</h1>
            <p class="text-xl text-gray-600">Fill in the details to reserve your activity</p>
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

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('booking.activities.store') }}">
                @csrf

                <div class="mb-6">
                    <label for="activity" class="block text-sm font-medium text-gray-700 mb-2">Activity</label>
                    <select name="activity_id" id="activity" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Activity</option>
                        @foreach($activities as $activity)
                            <option value="{{ $activity->id }}" 
                                    {{ request('activity') == $activity->id ? 'selected' : '' }}
                                    data-price="{{ $activity->price }}"
                                    data-max="{{ $activity->max_participants }}">
                                {{ $activity->name }} - ${{ number_format($activity->price, 2) }}/person
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Date & Time</label>
                        <input type="datetime-local" name="scheduled_time" id="scheduled_time" 
                               class="w-full border rounded-lg px-3 py-2" required
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                    <div>
                        <label for="participants" class="block text-sm font-medium text-gray-700 mb-2">Number of Participants</label>
                        <input type="number" name="participants" id="participants" 
                               class="w-full border rounded-lg px-3 py-2" min="1" value="1" required>
                        <p class="text-sm text-gray-500 mt-1">Max: <span id="max_participants">1</span> participants</p>
                    </div>
                </div>

                @guest
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-4">Guest Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="guest_name" id="guest_name" required
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="guest_email" id="guest_email" required
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div class="md:col-span-2">
                            <label for="guest_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                            <input type="tel" name="guest_phone" id="guest_phone" required
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>
                @endguest

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Special Requirements (Optional)</label>
                    <textarea name="notes" id="notes" rows="4" 
                              class="w-full border rounded-lg px-3 py-2" 
                              placeholder="Any special requirements or medical conditions..."></textarea>
                </div>

                <div class="mb-6">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="peer sr-only" required>
                            <div class="rounded-lg border-2 peer-checked:border-green-600 peer-checked:bg-green-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-credit-card text-2xl mb-2 text-gray-600 peer-checked:text-green-600"></i>
                                <div class="text-sm font-medium">Card</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-green-600 peer-checked:bg-green-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-money-bill text-2xl mb-2 text-gray-600 peer-checked:text-green-600"></i>
                                <div class="text-sm font-medium">Cash</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="online" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-green-600 peer-checked:bg-green-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-globe text-2xl mb-2 text-gray-600 peer-checked:text-green-600"></i>
                                <div class="text-sm font-medium">Online</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only">
                            <div class="rounded-lg border-2 peer-checked:border-green-600 peer-checked:bg-green-50 p-4 text-center hover:bg-gray-50">
                                <i class="fas fa-university text-2xl mb-2 text-gray-600 peer-checked:text-green-600"></i>
                                <div class="text-sm font-medium">Bank Transfer</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Price per person:</span>
                        <span id="price_per_person" class="font-semibold">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Number of participants:</span>
                        <span id="number_of_participants" class="font-semibold">1</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Amount:</span>
                            <span id="total_amount" class="text-2xl font-bold text-green-600">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        Complete Booking
                    </button>
                    <a href="{{ route('booking.activities.index') }}" 
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
    const activitySelect = document.getElementById('activity');
    const participantsInput = document.getElementById('participants');
    const pricePerPerson = document.getElementById('price_per_person');
    const numberOfParticipants = document.getElementById('number_of_participants');
    const totalAmount = document.getElementById('total_amount');
    const maxParticipantsSpan = document.getElementById('max_participants');

    // Activity data
    const activityData = {!! $activities->map(function($activity) {
        return [
            'id' => $activity->id,
            'name' => $activity->name,
            'price_per_person' => $activity->price,
            'max_participants' => $activity->max_participants
        ];
    })->toJson() !!};

    // Handle activity change
    activitySelect.addEventListener('change', function() {
        const selectedActivityId = this.value;
        const selectedActivity = activityData.find(a => a.id == selectedActivityId);
        
        if (selectedActivity) {
            pricePerPerson.textContent = `$${selectedActivity.price_per_person.toFixed(2)}`;
            maxParticipantsSpan.textContent = selectedActivity.max_participants;
            participantsInput.max = selectedActivity.max_participants;
            
            if (parseInt(participantsInput.value) > selectedActivity.max_participants) {
                participantsInput.value = selectedActivity.max_participants;
            }
        } else {
            pricePerPerson.textContent = '$0.00';
            maxParticipantsSpan.textContent = '1';
            participantsInput.max = '1';
        }
        updatePrice();
    });

    // Handle participants change
    participantsInput.addEventListener('input', updatePrice);

    function updatePrice() {
        const selectedActivityId = activitySelect.value;
        const selectedActivity = activityData.find(a => a.id == selectedActivityId);
        const participants = parseInt(participantsInput.value) || 1;
        
        if (selectedActivity && participants > 0) {
            const total = selectedActivity.price_per_person * participants;
            pricePerPerson.textContent = `$${selectedActivity.price_per_person.toFixed(2)}`;
            numberOfParticipants.textContent = participants;
            totalAmount.textContent = `$${total.toFixed(2)}`;
        } else {
            pricePerPerson.textContent = '$0.00';
            numberOfParticipants.textContent = '1';
            totalAmount.textContent = '$0.00';
        }
    }

    // Set initial activity if provided
    @if(request('activity'))
        activitySelect.value = '{{ request('activity') }}';
        activitySelect.dispatchEvent(new Event('change'));
    @endif
});
</script>
@endsection
