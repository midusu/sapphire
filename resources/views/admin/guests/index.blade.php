@extends('admin.layout')

@section('title', 'Guests - Sapphire Hotel Management')
@section('header', 'Guest Management')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Guest Management</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.guests.loyalty') }}"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-crown mr-2"></i>Loyalty Program
            </a>
            <a href="{{ route('admin.guests.vip') }}"
                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-star mr-2"></i>VIP Guests
            </a>
            <a href="{{ route('admin.guests.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-user-plus mr-2"></i>Add Guest
            </a>
        </div>
    </div>

    <!-- Guest Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Guests</p>
                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\User::where('role_id', 7)->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ \App\Models\User::where('role_id', 7)->whereMonth('created_at', now()->month)->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-user-plus text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Guests</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function ($query) {
        $query->whereIn('status', ['confirmed', 'checked_in']);
    })->count() }}
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-bed text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Repeat Guests</p>
                    <p class="text-2xl font-bold text-purple-600">
                        {{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function ($query) {
        $query->where('status', 'checked_out');
    }, '>', 1)->count() }}
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-redo text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">VIP Guests</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ \App\Models\User::where('role_id', 7)->whereHas('bookings', function ($query) {
        $query->where('status', 'checked_out');
    }, '>=', 10)->count() }}
                    </p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="{{ request('search') }}" class="w-full border rounded-lg px-3 py-2"
                    placeholder="Search by name, email, phone, or ID...">
            </div>

            <select name="loyalty_status" class="border rounded-lg px-3 py-2">
                <option value="">All Loyalty Levels</option>
                <option value="bronze" {{ request('loyalty_status') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                <option value="silver" {{ request('loyalty_status') == 'silver' ? 'selected' : '' }}>Silver</option>
                <option value="gold" {{ request('loyalty_status') == 'gold' ? 'selected' : '' }}>Gold</option>
                <option value="platinum" {{ request('loyalty_status') == 'platinum' ? 'selected' : '' }}>Platinum</option>
            </select>

            <select name="status" class="border rounded-lg px-3 py-2">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>

            <a href="{{ route('admin.guests.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
    </div>

    <!-- Today's Check-ins/Check-outs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Today's Check-ins</h3>
                <a href="{{ route('admin.guests.check-in-today') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="space-y-3">
                @php
                    $checkInGuests = \App\Models\User::where('role_id', 7)
                        ->whereHas('bookings', function ($query) {
                            $query->whereDate('check_in_date', now()->format('Y-m-d'))
                                ->whereIn('status', ['confirmed']);
                        })
                        ->with([
                            'bookings' => function ($query) {
                                $query->whereDate('check_in_date', now()->format('Y-m-d'))
                                    ->whereIn('status', ['confirmed'])
                                    ->with(['room.roomType']);
                            }
                        ])
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($checkInGuests as $guest)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 rounded-full p-2">
                                <i class="fas fa-user text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $guest->name }}</p>
                                <p class="text-sm text-gray-600">
                                    @if($guest->bookings->first() && $guest->bookings->first()->room)
                                        {{ $guest->bookings->first()->room->room_number }} •
                                        {{ $guest->bookings->first()->room->roomType->name ?? 'N/A' }}
                                    @else
                                        No active booking
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($guest->bookings->first())
                            <a href="{{ route('admin.bookings.show', $guest->bookings->first()) }}"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No check-ins today</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Today's Check-outs</h3>
                <a href="{{ route('admin.guests.check-out-today') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            <div class="space-y-3">
                @php
                    $checkOutGuests = \App\Models\User::where('role_id', 7)
                        ->whereHas('bookings', function ($query) {
                            $query->whereDate('check_out_date', now()->format('Y-m-d'))
                                ->whereIn('status', ['confirmed', 'checked_in']);
                        })
                        ->with([
                            'bookings' => function ($query) {
                                $query->whereDate('check_out_date', now()->format('Y-m-d'))
                                    ->whereIn('status', ['confirmed', 'checked_in'])
                                    ->with(['room.roomType']);
                            }
                        ])
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($checkOutGuests as $guest)
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="bg-orange-100 rounded-full p-2">
                                <i class="fas fa-sign-out-alt text-orange-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $guest->name }}</p>
                                <p class="text-sm text-gray-600">
                                    @if($guest->bookings->first() && $guest->bookings->first()->room)
                                        {{ $guest->bookings->first()->room->room_number }} •
                                        {{ $guest->bookings->first()->room->roomType->name ?? 'N/A' }}
                                    @else
                                        No active booking
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($guest->bookings->first())
                            <a href="{{ route('admin.bookings.show', $guest->bookings->first()) }}"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No check-outs today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Guests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loyalty
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($guests as $guest)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-500"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $guest->name }}</div>
                                                    @if($guest->nationality)
                                                        <div class="text-sm text-gray-500">{{ $guest->nationality }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $guest->email }}</div>
                                            @if($guest->phone)
                                                <div class="text-sm text-gray-500">{{ $guest->phone }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $guest->bookings->count() }} bookings
                                            @if($guest->bookings->where('status', 'checked_out')->count() > 0)
                                                <div class="text-xs text-gray-500">
                                                    {{ $guest->bookings->where('status', 'checked_out')->count() }} completed
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            ${{ number_format(\App\Models\Payment::whereHas('booking', function ($query) use ($guest) {
                            $query->where('user_id', $guest->id);
                        })->orWhereHas('activityBooking', function ($query) use ($guest) {
                            $query->where('user_id', $guest->id);
                        })->where('status', 'completed')->sum('amount'), 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $totalBookings = $guest->bookings->count();
                                                $totalSpent = \App\Models\Payment::whereHas('booking', function ($query) use ($guest) {
                                                    $query->where('user_id', $guest->id);
                                                })->orWhereHas('activityBooking', function ($query) use ($guest) {
                                                    $query->where('user_id', $guest->id);
                                                })->where('status', 'completed')->sum('amount');
                                                $loyaltyStatus = 'bronze';
                                                if ($totalBookings >= 20 || $totalSpent >= 10000) {
                                                    $loyaltyStatus = 'platinum';
                                                } elseif ($totalBookings >= 10 || $totalSpent >= 5000) {
                                                    $loyaltyStatus = 'gold';
                                                } elseif ($totalBookings >= 5 || $totalSpent >= 2000) {
                                                    $loyaltyStatus = 'silver';
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($loyaltyStatus == 'bronze') bg-orange-100 text-orange-800
                                                    @elseif($loyaltyStatus == 'silver') bg-gray-100 text-gray-800
                                                    @elseif($loyaltyStatus == 'gold') bg-yellow-100 text-yellow-800
                                                    @else bg-purple-100 text-purple-800
                                                    @endif">
                                                {{ ucfirst($loyaltyStatus) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($guest->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists())
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.guests.show', $guest) }}"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.guests.edit', $guest) }}"
                                                    class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.guests.destroy', $guest) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this guest?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.bookings.create') }}?guest_id={{ $guest->id }}"
                                                    class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No guests found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                {{ $guests->links() }}
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium">{{ $guests->firstItem() }}</span>
                        to
                        <span class="font-medium">{{ $guests->lastItem() }}</span>
                        of
                        <span class="font-medium">{{ $guests->total() }}</span>
                        guests
                    </p>
                </div>
                <div>
                    {{ $guests->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 flex space-x-4">
        <a href="{{ route('admin.guests.check-in-today') }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-sign-in-alt mr-2"></i>Today's Check-ins
        </a>
        <a href="{{ route('admin.guests.check-out-today') }}"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="fas fa-sign-out-alt mr-2"></i>Today's Check-outs
        </a>
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-print mr-2"></i>Print List
        </button>
    </div>
@endsection