<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sapphire Hotel Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold text-blue-400">Sapphire</h1>
                <p class="text-sm text-gray-400">Hotel Management</p>
            </div>
            <nav class="mt-8" style="background: #1e293b;">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="{{ route('admin.bookings.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.bookings.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-calendar-check mr-2"></i> Bookings
                </a>
                <a href="{{ route('admin.activities.calendar') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.activities.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-hiking mr-2"></i> Activities
                </a>
                <a href="{{ route('admin.rooms.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.rooms.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-bed mr-2"></i> Rooms
                </a>
                <a href="{{ route('admin.guests.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.guests.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-users mr-2"></i> Guests
                </a>
                <a href="{{ route('admin.payments.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.payments.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-credit-card mr-2"></i> Payments
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.coupons.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-tags mr-2"></i> Coupons
                </a>
                <a href="{{ route('admin.kitchen.dashboard') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.kitchen.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-utensils mr-2"></i> Kitchen
                </a>
                <a href="{{ route('admin.rooms.housekeeping') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.rooms.housekeeping') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-broom mr-2"></i> Housekeeping
                </a>
                <a href="{{ route('admin.payments.reports') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.payments.reports') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i> Reports
                </a>
                <a href="{{ route('admin.gallery.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.gallery.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-images mr-2"></i> Gallery
                </a>
                <a href="{{ route('admin.amenities.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.amenities.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-star mr-2"></i> Amenities
                </a>

                <div class="px-4 py-2 mt-4 mb-2 text-xs text-gray-400 uppercase font-semibold">
                    Security
                </div>

                <!-- Security Menu with Submenu -->
                <div x-data="{ open: {{ request()->routeIs('admin.security.*') ? 'true' : 'false' }}" class="relative">
                    <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.security.*') ? 'bg-slate-700' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i> Security
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform -translate-y-1"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-1"
                         class="ml-4 mt-1 space-y-1 bg-slate-800 relative z-10">
                        <a href="{{ route('admin.security.audit-logs.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.security.audit-logs.*') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-clipboard-list mr-2"></i> Audit Logs
                        </a>
                        <a href="{{ route('admin.security.backup.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.security.backup.*') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-database mr-2"></i> Backup & Restore
                        </a>
                        <a href="{{ route('admin.security.activity-safety.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.security.activity-safety.*') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Activity Safety Logs
                        </a>
                    </div>
                </div>

                <div class="px-4 py-2 mt-4 mb-2 text-xs text-gray-400 uppercase font-semibold">
                    Operations
                </div>

                <!-- Inventory Menu with Submenu -->
                <div x-data="{ open: {{ request()->routeIs('admin.inventory.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.food.items.*') ? 'true' : 'false' }}" class="relative">
                    <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.inventory.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.food.items.*') ? 'bg-slate-700' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-boxes mr-2"></i> Inventory
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 transform -translate-y-1"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-1"
                         class="ml-4 mt-1 space-y-1 bg-slate-800 relative z-10">
                        <a href="{{ route('admin.inventory.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.inventory.index') || request()->routeIs('admin.inventory.create') || request()->routeIs('admin.inventory.edit') || request()->routeIs('admin.inventory.history') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-list mr-2"></i> Stock Items
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.suppliers.*') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-truck mr-2"></i> Suppliers
                        </a>
                        <a href="{{ route('admin.food.items.index') }}"
                            class="block px-4 py-2 hover:bg-slate-600 text-white {{ request()->routeIs('admin.food.items.*') ? 'bg-slate-600' : '' }}">
                            <i class="fas fa-hamburger mr-2"></i> Food Items
                        </a>
                    </div>
                </div>
                <a href="{{ route('admin.food.orders.index') }}"
                    class="block px-4 py-2 hover:bg-slate-700 {{ request()->routeIs('admin.food.orders.*') ? 'bg-slate-700' : '' }}">
                    <i class="fas fa-receipt mr-2"></i> Food Orders
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.notifications.index') }}" class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            @php
                                $unreadCount = Auth::check() ? \App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count() : 0;
                            @endphp
                            @if($unreadCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>
                        <div class="flex items-center space-x-2">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">
                                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ Auth::check() && Auth::user()->role ? Auth::user()->role->name : 'Administrator' }}
                                </p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ Auth::check() ? Auth::user()->name : 'Admin' }}&background=0D47A1&color=fff"
                                alt="Avatar" class="h-8 w-8 rounded-full">
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>