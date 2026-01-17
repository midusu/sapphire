@extends('admin.layout')

@section('title', 'Guest Loyalty - Sapphire Hotel Management')
@section('header', 'Guest Loyalty Program')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Guest Loyalty Program</h2>
    <p class="text-gray-600">Manage guest loyalty tiers and rewards</p>
</div>

<!-- Loyalty Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Bronze Members</p>
                <p class="text-2xl font-bold text-orange-600">{{ $guests->where('loyalty_status', 'bronze')->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-medal text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Silver Members</p>
                <p class="text-2xl font-bold text-gray-600">{{ $guests->where('loyalty_status', 'silver')->count() }}</p>
            </div>
            <div class="bg-gray-100 rounded-full p-3">
                <i class="fas fa-award text-gray-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Gold Members</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $guests->where('loyalty_status', 'gold')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-trophy text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Platinum Members</p>
                <p class="text-2xl font-bold text-purple-600">{{ $guests->where('loyalty_status', 'platinum')->count() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-crown text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Loyalty Program Information -->
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6 mb-6 text-white">
    <h3 class="text-xl font-bold mb-4">Loyalty Program Tiers</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white bg-opacity-20 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-medal mr-2"></i>
                <h4 class="font-semibold">Bronze</h4>
            </div>
            <p class="text-sm opacity-90">New guests</p>
            <p class="text-xs opacity-75 mt-1">0-4 bookings or under $2,000</p>
        </div>
        <div class="bg-white bg-opacity-20 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-award mr-2"></i>
                <h4 class="font-semibold">Silver</h4>
            </div>
            <p class="text-sm opacity-90">Regular guests</p>
            <p class="text-xs opacity-75 mt-1">5-9 bookings or $2,000-$4,999</p>
        </div>
        <div class="bg-white bg-opacity-20 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-trophy mr-2"></i>
                <h4 class="font-semibold">Gold</h4>
            </div>
            <p class="text-sm opacity-90">Valued guests</p>
            <p class="text-xs opacity-75 mt-1">10-19 bookings or $5,000-$9,999</p>
        </div>
        <div class="bg-white bg-opacity-20 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-crown mr-2"></i>
                <h4 class="font-semibold">Platinum</h4>
            </div>
            <p class="text-sm opacity-90">Elite guests</p>
            <p class="text-xs opacity-75 mt-1">20+ bookings or $10,000+</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="loyalty_status" class="border rounded-lg px-3 py-2">
            <option value="">All Loyalty Levels</option>
            <option value="bronze" {{ request('loyalty_status') == 'bronze' ? 'selected' : '' }}>Bronze</option>
            <option value="silver" {{ request('loyalty_status') == 'silver' ? 'selected' : '' }}>Silver</option>
            <option value="gold" {{ request('loyalty_status') == 'gold' ? 'selected' : '' }}>Gold</option>
            <option value="platinum" {{ request('loyalty_status') == 'platinum' ? 'selected' : '' }}>Platinum</option>
        </select>
        
        <select name="min_bookings" class="border rounded-lg px-3 py-2">
            <option value="">Min Bookings</option>
            <option value="5" {{ request('min_bookings') == '5' ? 'selected' : '' }}>5+ bookings</option>
            <option value="10" {{ request('min_bookings') == '10' ? 'selected' : '' }}>10+ bookings</option>
            <option value="20" {{ request('min_bookings') == '20' ? 'selected' : '' }}>20+ bookings</option>
        </select>
        
        <select name="min_spent" class="border rounded-lg px-3 py-2">
            <option value="">Min Spent</option>
            <option value="1000" {{ request('min_spent') == '1000' ? 'selected' : '' }}>$1,000+</option>
            <option value="5000" {{ request('min_spent') == '5000' ? 'selected' : '' }}>$5,000+</option>
            <option value="10000" {{ request('min_spent') == '10000' ? 'selected' : '' }}>$10,000+</option>
        </select>
        
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        
        <a href="{{ route('admin.guests.loyalty') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-times"></i> Clear
        </a>
    </form>
</div>

<!-- Top Guests by Spending -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Guests by Loyalty & Spending</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loyalty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            {{ $guest->total_bookings }}
                            @if($guest->bookings->where('status', 'checked_out')->count() > 0)
                                <div class="text-xs text-gray-500">
                                    {{ $guest->bookings->where('status', 'checked_out')->count() }} completed
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${{ number_format($guest->total_spent, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($guest->loyalty_status == 'bronze') bg-orange-100 text-orange-800
                                @elseif($guest->loyalty_status == 'silver') bg-gray-100 text-gray-800
                                @elseif($guest->loyalty_status == 'gold') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                <i class="fas fa-crown mr-1"></i>{{ ucfirst($guest->loyalty_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $guest->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.guests.show', $guest) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.create') }}?guest_id={{ $guest->id }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-plus"></i>
                                </a>
                                @if($guest->loyalty_status == 'gold' || $guest->loyalty_status == 'platinum')
                                    <button class="text-purple-600 hover:text-purple-900" title="VIP Guest">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
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

<!-- Loyalty Benefits -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Loyalty Benefits</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="border rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-medal text-orange-600 mr-2"></i>
                <h4 class="font-semibold text-gray-800">Bronze Benefits</h4>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Standard room rates</li>
                <li>• Basic amenities</li>
                <li>• Email newsletters</li>
            </ul>
        </div>
        <div class="border rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-award text-gray-600 mr-2"></i>
                <h4 class="font-semibold text-gray-800">Silver Benefits</h4>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• 5% discount on room rates</li>
                <li>• Priority check-in</li>
                <li>• Room upgrade when available</li>
                <li>• Welcome drink</li>
            </ul>
        </div>
        <div class="border rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-trophy text-yellow-600 mr-2"></i>
                <h4 class="font-semibold text-gray-800">Gold Benefits</h4>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• 10% discount on room rates</li>
                <li>• Express check-in/out</li>
                <li>• Guaranteed room upgrade</li>
                <li>• Free breakfast</li>
                <li>• Late checkout (2 PM)</li>
            </ul>
        </div>
        <div class="border rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-crown text-purple-600 mr-2"></i>
                <h4 class="font-semibold text-gray-800">Platinum Benefits</h4>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• 15% discount on room rates</li>
                <li>• VIP check-in service</li>
                <li>• Suite upgrade when available</li>
                <li>• Free breakfast & dinner</li>
                <li>• 24/7 concierge service</li>
                <li>• Exclusive events access</li>
            </ul>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 flex space-x-4">
    <a href="{{ route('admin.guests.vip') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
        <i class="fas fa-star mr-2"></i>VIP Guests
    </a>
    <a href="{{ route('admin.guests.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2"></i>Back to Guests
    </a>
</div>
@endsection
