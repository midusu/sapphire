@extends('layouts.app')

@section('title', 'My Stay - Sapphire Hotel')

@section('content')
    <div class="min-h-screen bg-gray-50 pb-12">
        <!-- Hero Section -->
        <div class="bg-white text-gray-900 relative overflow-hidden border-b border-gray-200">
            <div class="absolute inset-0 opacity-30 bg-repeat"
                style="opacity: 5%;background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-3xl font-light">
                            Good
                            {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 18 ? 'Afternoon' : 'Evening') }},
                            <span class="font-bold">{{ Auth::user()->name }}</span>
                        </h1>
                        <p class="text-gray-600 mt-2">Welcome to your personal concierge dashboard.</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center space-x-4">
                        @if(Auth::user()->unreadNotifications()->count() > 0)
                            <a href="{{ route('guest.notifications') }}"
                                class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                                <i class="fas fa-bell text-2xl"></i>
                                <span
                                    class="absolute top-0 right-0 h-3 w-3 bg-red-500 rounded-full border-2 border-blue-900"></span>
                            </a>
                        @endif
                        <div class="text-right hidden md:block">
                            <div class="text-xs text-gray-500 uppercase tracking-widest">Today's Date</div>
                            <div class="text-lg font-medium">{{ now()->format('l, F jS') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Current Stay Card (Hero Overlay) -->
                @if($currentBooking)
                    <div class="mt-8 bg-blue-50 backdrop-blur-md rounded-xl p-6 border border-blue-100 text-gray-900">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                                    <i class="fas fa-key text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide">Room Number</div>
                                    <div class="text-2xl font-bold">{{ $currentBooking->room->room_number }}</div>
                                    <div class="text-sm text-gray-600">{{ $currentBooking->room->roomType->name }}</div>
                                </div>
                            </div>
                            <div class="h-10 w-px bg-gray-200 hidden md:block"></div>
                            <div class="text-center md:text-left">
                                <div class="text-sm text-gray-500 uppercase tracking-wide">Check-out</div>
                                <div class="text-xl font-medium">{{ $currentBooking->check_out_date->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">11:00 AM</div>
                            </div>
                            <div class="h-10 w-px bg-gray-200 hidden md:block"></div>
                            <div class="flex gap-3">
                                <a href="{{ route('guest.food-menu') }}"
                                    class="px-5 py-2.5 bg-white text-blue-900 border border-gray-200 rounded-lg font-semibold hover:bg-gray-50 transition shadow-sm">
                                    Order Room Service
                                </a>
                                <a href="{{ route('booking.activities.index') }}"
                                    class="px-5 py-2.5 bg-blue-800 text-white border border-blue-700 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Book Activity
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-8 bg-blue-50 backdrop-blur-md rounded-xl p-6 border border-blue-100 text-gray-900">
                        <p class="text-center text-lg">You currently have no active stay with us.</p>
                        <div class="text-center mt-4">
                            <a href="{{ route('booking.rooms.index') }}"
                                class="px-6 py-2 bg-white text-blue-900 rounded-lg font-bold hover:bg-blue-50 transition">Book a
                                Room</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Spend -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Current Spend</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalSpent, 2) }}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-full">
                        <i class="fas fa-receipt text-green-600"></i>
                    </div>
                </div>

                <!-- Upcoming Activities Count -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Upcoming Activities</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $upcomingActivities->count() }}</p>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-orange-600"></i>
                    </div>
                </div>

                <!-- Loyalty / Nights (Placeholder) -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nights Stayed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $currentBooking ? $currentBooking->duration : 0 }}
                        </p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-full">
                        <i class="fas fa-moon text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Feed -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Upcoming Activities -->
                    @if($upcomingActivities->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="font-semibold text-gray-800">Your Agenda</h3>
                                <a href="{{ route('booking.activities.index') }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All</a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach($upcomingActivities as $activity)
                                    <div class="p-6 flex items-start gap-4 hover:bg-gray-50 transition">
                                        <div
                                            class="bg-blue-100 text-blue-600 rounded-lg w-12 h-12 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-hiking"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $activity->activity->name }}</h4>
                                            <div class="flex items-center text-sm text-gray-500 mt-1 gap-4">
                                                <span><i class="far fa-clock mr-1"></i>
                                                    {{ $activity->scheduled_time->format('M j, H:i') }}</span>
                                                <span><i class="fas fa-users mr-1"></i> {{ $activity->participants }} People</span>
                                            </div>
                                        </div>
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Confirmed</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recent Orders -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-semibold text-gray-800">Recent Food Orders</h3>
                            <a href="{{ route('guest.my-orders') }}"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">Order History</a>
                        </div>
                        @if($recentOrders->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach($recentOrders as $order)
                                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="bg-yellow-50 text-yellow-600 rounded-lg w-12 h-12 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $order->food->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }} •
                                                    {{ $order->getMenuTypeLabel() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold text-gray-900">${{ number_format($order->total_price, 2) }}
                                            </div>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium capitalize 
                                                                                @if($order->status == 'delivered') bg-gray-100 text-gray-600 
                                                                                @elseif($order->status == 'cancelled') bg-red-50 text-red-600
                                                                                @else bg-blue-50 text-blue-600 @endif">
                                                {{ $order->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center text-gray-500">
                                <p>No recent orders. <a href="{{ route('guest.food-menu') }}"
                                        class="text-blue-600 font-medium hover:underline">View Menu</a></p>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions Grid -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('guest.food-menu') }}"
                                class="p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition text-center group">
                                <i
                                    class="fas fa-pizza-slice text-xl mb-2 text-gray-400 group-hover:text-blue-600 transition"></i>
                                <div class="text-sm font-medium">Order Food</div>
                            </a>
                            <a href="{{ route('booking.activities.index') }}"
                                class="p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition text-center group">
                                <i
                                    class="fas fa-swimmer text-xl mb-2 text-gray-400 group-hover:text-blue-600 transition"></i>
                                <div class="text-sm font-medium">Activities</div>
                            </a>
                            <a href="{{ route('guest.my-orders') }}"
                                class="p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition text-center group">
                                <i
                                    class="fas fa-list-alt text-xl mb-2 text-gray-400 group-hover:text-blue-600 transition"></i>
                                <div class="text-sm font-medium">My Orders</div>
                            </a>
                            <a href="#"
                                class="p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition text-center group">
                                <i class="fas fa-broom text-xl mb-2 text-gray-400 group-hover:text-blue-600 transition"></i>
                                <div class="text-sm font-medium">Housekeeping</div>
                            </a>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                            @if($unreadNotifications->count() > 0)
                                <span
                                    class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">{{ $unreadNotifications->count() }}
                                    New</span>
                            @endif
                        </div>
                        @if($unreadNotifications->count() > 0)
                            <div class="space-y-4">
                                @foreach($unreadNotifications as $notification)
                                    <div class="flex gap-3 items-start">
                                        <div class="w-2 h-2 mt-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                                        <div>
                                            <p class="text-sm text-gray-800 font-medium">{{ $notification->title }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                <a href="{{ route('guest.notifications') }}"
                                    class="block text-center text-sm text-blue-600 hover:underline mt-4">View All</a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">No new notifications.</p>
                        @endif
                    </div>

                    <!-- Service Card -->
                    <div
                        class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-xl shadow-sm p-6 text-center text-white">
                        <i class="fas fa-headset text-3xl mb-3 opacity-80"></i>
                        <h3 class="font-semibold mb-1">Need Assistance?</h3>
                        <p class="text-sm text-blue-200 mb-4">Our front desk is available 24/7.</p>
                        <button
                            class="bg-white text-blue-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-50 transition w-full">Call
                            Reception</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection