@extends('admin.layout')

@section('title', 'New Activity Booking - Sapphire Hotel Management')
@section('header', 'Create Activity Booking')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('admin.activities.bookings.store') }}">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link to Room Booking (Optional)</label>
                    <select name="booking_id" id="booking_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">No room booking</option>
                        @foreach($bookings as $booking)
                            <option value="{{ $booking->id }}">
                                {{ $booking->user ? $booking->user->name : ($booking->guest_name ?? 'Guest') }} - Room {{ $booking->room->room_number }} ({{ $booking->check_in_date->format('M d') }} to {{ $booking->check_out_date->format('M d') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Activity Selection -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity Selection</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activity</label>
                    <select name="activity_id" id="activity_id" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Activity</option>
                        @foreach($activities as $activity)
                            <option value="{{ $activity->id }}" 
                                    data-price="{{ $activity->price }}" 
                                    data-max="{{ $activity->max_participants }}"
                                    data-duration="{{ $activity->duration }}">
                                {{ $activity->name }} - ${{ number_format($activity->price, 2) }}/person
                            </option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" id="date" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>
            
            <!-- Activity Details -->
            <div id="activityDetails" class="hidden mb-4 p-4 bg-blue-50 rounded-lg">
                <div id="activityInfo"></div>
            </div>
            
            <!-- Time Slots -->
            <div id="timeSlots" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Available Time Slots</label>
                <div id="slotsList" class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <!-- Time slots will be loaded here -->
                </div>
            </div>
            
            <!-- Price Calculation -->
            <div id="priceCalculation" class="mt-4 p-4 bg-green-50 rounded-lg hidden">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Amount:</span>
                    <span class="text-2xl font-bold text-green-600">$<span id="totalAmount">0.00</span></span>
                </div>
            </div>
        </div>

        <!-- Participant Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Participant Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Participants</label>
                    <input type="number" name="participants" id="participants" min="1" value="1" class="w-full border rounded-lg px-3 py-2" required>
                    @error('participants')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                    <input type="datetime-local" name="scheduled_time" id="scheduled_time" class="w-full border rounded-lg px-3 py-2" required>
                    @error('scheduled_time')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Special Notes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Notes</h3>
            <textarea name="notes" rows="4" class="w-full border rounded-lg px-3 py-2" placeholder="Any special requirements or notes..."></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.activities.bookings.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Create Booking
            </button>
        </div>
    </form>
</div>

<script>
// Set minimum date to today
document.getElementById('date').min = new Date().toISOString().split('T')[0];

// Load activity details when activity is selected
document.getElementById('activity_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const activityDetails = document.getElementById('activityDetails');
    const activityInfo = document.getElementById('activityInfo');
    
    if (this.value) {
        activityInfo.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm text-gray-600">Price per person:</span>
                    <p class="font-semibold">$${parseFloat(selected.dataset.price).toFixed(2)}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Max participants:</span>
                    <p class="font-semibold">${selected.dataset.max}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Duration:</span>
                    <p class="font-semibold">${selected.dataset.duration}</p>
                </div>
            </div>
        `;
        activityDetails.classList.remove('hidden');
        
        // Update max participants
        document.getElementById('participants').max = selected.dataset.max;
    } else {
        activityDetails.classList.add('hidden');
    }
});

// Load time slots when date is selected
document.getElementById('date').addEventListener('change', loadTimeSlots);

function loadTimeSlots() {
    const activityId = document.getElementById('activity_id').value;
    const date = document.getElementById('date').value;
    
    if (activityId && date) {
        fetch(`/admin/activities/available-slots?activity_id=${activityId}&date=${date}`)
            .then(response => response.json())
            .then(slots => {
                const slotsList = document.getElementById('slotsList');
                const timeSlotsDiv = document.getElementById('timeSlots');
                
                if (slots.length > 0) {
                    slotsList.innerHTML = slots.map(slot => `
                        <div class="border rounded-lg p-2 cursor-pointer hover:bg-blue-50 transition ${!slot.is_available ? 'opacity-50 cursor-not-allowed' : ''}" 
                             onclick="selectTimeSlot('${slot.time}', '${slot.end_time}', ${slot.available}, ${slot.is_available})"
                             data-available="${slot.available}">
                            <div class="text-sm font-medium">${slot.time} - ${slot.end_time}</div>
                            <div class="text-xs ${slot.is_available ? 'text-green-600' : 'text-red-600'}">
                                ${slot.available} slots available
                            </div>
                        </div>
                    `).join('');
                    timeSlotsDiv.classList.remove('hidden');
                } else {
                    slotsList.innerHTML = '<div class="col-span-4 text-center text-gray-500 py-4">No time slots available</div>';
                    timeSlotsDiv.classList.remove('hidden');
                }
            });
    }
}

function selectTimeSlot(startTime, endTime, available, isAvailable) {
    if (!isAvailable) return;
    
    // Remove previous selections
    document.querySelectorAll('#slotsList > div').forEach(div => {
        div.classList.remove('bg-blue-50', 'border-blue-500');
    });
    
    // Add selection to clicked slot
    event.target.closest('div').classList.add('bg-blue-50', 'border-blue-500');
    
    // Set the scheduled time
    const date = document.getElementById('date').value;
    document.getElementById('scheduled_time').value = `${date}T${startTime}:00`;
    
    calculateTotal();
}

function calculateTotal() {
    const activityId = document.getElementById('activity_id').value;
    const participants = parseInt(document.getElementById('participants').value) || 0;
    
    if (activityId && participants > 0) {
        const selected = document.querySelector(`#activity_id option[value="${activityId}"]`);
        const price = parseFloat(selected.dataset.price);
        const total = price * participants;
        
        document.getElementById('totalAmount').textContent = total.toFixed(2);
        document.getElementById('priceCalculation').classList.remove('hidden');
    }
}

// Recalculate when participants change
document.getElementById('participants').addEventListener('input', calculateTotal);

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const scheduledTime = document.getElementById('scheduled_time').value;
    if (!scheduledTime) {
        e.preventDefault();
        alert('Please select a time slot');
    }
});
</script>
@endsection
