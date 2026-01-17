@extends('admin.layout')

@section('title', 'Guest Details - Sapphire Hotel Management')
@section('header', 'Guest Details')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Guest Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-user text-gray-500 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $guest->name }}</h2>
                        <p class="text-gray-600">{{ $guest->email }}</p>
                        @if($guest->phone)
                            <p class="text-gray-600">{{ $guest->phone }}</p>
                        @endif
                        <div class="flex items-center space-x-2 mt-2">
                        @php
                            $loyaltyStatus = $guest->getLoyaltyStatus();
                        @endphp
                        <span class="px-3 py-1 text-sm rounded-full font-semibold
                             @if($loyaltyStatus == 'bronze') bg-orange-100 text-orange-800
                             @elseif($loyaltyStatus == 'silver') bg-gray-100 text-gray-800
                             @elseif($loyaltyStatus == 'gold') bg-yellow-100 text-yellow-800
                             @else bg-purple-100 text-purple-800
                             @endif">
                            <i class="fas fa-crown mr-1"></i>{{ ucfirst($loyaltyStatus) }} Member
                        </span>
                            @if($guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists())
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 font-semibold">
                                    <i class="fas fa-bed mr-1"></i>Active Guest
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.bookings.create') }}?guest_id={{ $guest->id }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>New Booking
                    </a>
                    <a href="{{ route('admin.guests.edit', $guest) }}"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('admin.guests.index') }}"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Guests
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Guest Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Email</span>
                        <p class="font-medium">{{ $guest->email }}</p>
                    </div>
                    @if($guest->phone)
                        <div>
                            <span class="text-sm text-gray-500">Phone</span>
                            <p class="font-medium">{{ $guest->phone }}</p>
                        </div>
                    @endif
                    @if($guest->address)
                        <div>
                            <span class="text-sm text-gray-500">Address</span>
                            <p class="font-medium">{{ $guest->address }}</p>
                        </div>
                    @endif
                    @if($guest->nationality)
                        <div>
                            <span class="text-sm text-gray-500">Nationality</span>
                            <p class="font-medium">{{ $guest->nationality }}</p>
                        </div>
                    @endif
                    @if($guest->date_of_birth)
                        <div>
                            <span class="text-sm text-gray-500">Date of Birth</span>
                            <p class="font-medium">{{ $guest->date_of_birth->format('M d, Y') }}
                                ({{ $guest->date_of_birth->age }} years)</p>
                        </div>
                    @endif
                    @if($guest->gender)
                        <div>
                            <span class="text-sm text-gray-500">Gender</span>
                            <p class="font-medium">{{ ucfirst($guest->gender) }}</p>
                        </div>
                    @endif
                    @if($guest->id_number)
                        <div>
                            <span class="text-sm text-gray-500">ID Number</span>
                            <p class="font-medium">{{ $guest->id_number }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-sm text-gray-500">Member Since</span>
                        <p class="font-medium">{{ $guest->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Guest Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Statistics</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $stats['total_bookings'] }}</div>
                        <div class="text-sm text-gray-600">Total Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">${{ number_format($stats['total_spent'], 2) }}</div>
                        <div class="text-sm text-gray-600">Total Spent</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $stats['total_stays'] }}</div>
                        <div class="text-sm text-gray-600">Completed Stays</div>
                    </div>
                    @if($stats['favorite_room_type'])
                        <div class="text-center">
                            <div class="text-lg font-bold text-orange-600">{{ $stats['favorite_room_type'] }}</div>
                            <div class="text-sm text-gray-600">Favorite Room Type</div>
                        </div>
                    @endif
                    @if($stats['last_visit'])
                        <div class="text-center">
                            <div class="text-lg font-bold text-gray-600">{{ $stats['last_visit']->diffForHumans() }}</div>
                            <div class="text-sm text-gray-600">Last Visit</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Emergency Contact</h3>
                @if($guest->emergency_contact || $guest->emergency_phone)
                    <div class="space-y-3">
                        @if($guest->emergency_contact)
                            <div>
                                <span class="text-sm text-gray-500">Contact Name</span>
                                <p class="font-medium">{{ $guest->emergency_contact }}</p>
                            </div>
                        @endif
                        @if($guest->emergency_phone)
                            <div>
                                <span class="text-sm text-gray-500">Contact Phone</span>
                                <p class="font-medium">{{ $guest->emergency_phone }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No emergency contact information</p>
                @endif
            </div>
        </div>

        <!-- Notes -->
        @if($guest->notes)
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Notes</h3>
                <p class="text-gray-700">{{ $guest->notes }}</p>
            </div>
        @endif

        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Booking ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Check-in</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Check-out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($guest->bookings->take(10) as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->room->room_number }} ({{ $booking->room->roomType->name }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->check_in_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->check_out_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${{ number_format($booking->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                                            @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No bookings found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($guest->bookings->count() > 10)
                <div class="px-6 py-3 border-t">
                    <a href="{{ route('admin.bookings.index') }}?guest_id={{ $guest->id }}"
                        class="text-blue-600 hover:text-blue-800 text-sm">
                        View all bookings →
                    </a>
                </div>
            @endif
        </div>

        <!-- Activity Bookings -->
        @if($guest->activityBookings->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Activity Bookings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Scheduled Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($guest->activityBookings->take(5) as $activityBooking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $activityBooking->activity->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $activityBooking->scheduled_time->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $activityBooking->participants }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($activityBooking->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($activityBooking->status == 'confirmed') bg-green-100 text-green-800
                                                    @elseif($activityBooking->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($activityBooking->status == 'completed') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                            {{ ucfirst($activityBooking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.activities.bookings.show', $activityBooking) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Payment History -->
        @if($guest->payments->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($guest->payments->take(10) as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($payment->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($payment->status == 'completed') bg-green-100 text-green-800
                                                    @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.payments.show', $payment) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Feedback History -->
        @php $feedbacks = \App\Models\Feedback::where('user_id', $guest->id)->orderBy('created_at', 'desc')->get(); @endphp
        @if($feedbacks->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Feedback & Complaints</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($feedbacks as $fb)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $fb->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $fb->type == 'complaint' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($fb->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ Str::limit($fb->subject, 40) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($fb->status == 'resolved') bg-green-100 text-green-800
                                                    @elseif($fb->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                            {{ ucfirst($fb->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.feedback.show', $fb) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection