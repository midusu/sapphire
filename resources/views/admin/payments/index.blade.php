@extends('admin.layout')

@section('title', 'Payments - Sapphire Hotel Management')
@section('header', 'Payment Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Payment Management</h2>
    <div class="space-x-2">
        <a href="{{ route('admin.payments.reports') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-chart-line mr-2"></i>Reports
        </a>
        <a href="{{ route('admin.payments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Record Payment
        </a>
    </div>
</div>

<!-- Payment Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Revenue</p>
                <p class="text-2xl font-bold text-indigo-600">${{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
                <i class="fas fa-wallet text-indigo-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Today's Revenue</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format(\App\Models\Payment::whereDate('created_at', now()->format('Y-m-d'))->where('status', 'completed')->sum('amount'), 2) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pending Payments</p>
                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Payment::where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Completed Today</p>
                <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Payment::whereDate('created_at', now()->format('Y-m-d'))->where('status', 'completed')->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-check text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Refunded</p>
                <p class="text-2xl font-bold text-red-600">{{ \App\Models\Payment::where('status', 'refunded')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-undo text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
        
        <select name="payment_method" class="border rounded px-3 py-2">
            <option value="">All Methods</option>
            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
        </select>
        
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-3 py-2" placeholder="From">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-3 py-2" placeholder="To">
        
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        
        <a href="{{ route('admin.payments.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-times"></i> Clear
        </a>
    </form>
</div>

<!-- Payments Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    @php
                        $guestName = 'Guest';
                        $guestEmail = 'N/A';
                        
                        if ($payment->booking) {
                            if ($payment->booking->user) {
                                $guestName = $payment->booking->user->name;
                                $guestEmail = $payment->booking->user->email;
                            } else {
                                $guestName = $payment->booking->guest_name ?? 'Guest';
                                $guestEmail = $payment->booking->guest_email ?? 'N/A';
                            }
                        } elseif ($payment->activityBooking) {
                            if ($payment->activityBooking->user) {
                                $guestName = $payment->activityBooking->user->name;
                                $guestEmail = $payment->activityBooking->user->email;
                            } elseif (!empty($payment->activityBooking->special_requirements)) {
                                $reqs = is_string($payment->activityBooking->special_requirements) 
                                    ? json_decode($payment->activityBooking->special_requirements, true) 
                                    : $payment->activityBooking->special_requirements;
                                    
                                if (is_array($reqs)) {
                                    $guestName = $reqs['guest_name'] ?? 'Guest';
                                    $guestEmail = $reqs['guest_email'] ?? 'N/A';
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $guestName }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $guestEmail }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->booking_id)
                                <div>
                                    <span class="text-sm text-gray-900">Room Booking</span>
                                    <div class="text-xs text-blue-600">Room {{ $payment->booking->room->room_number }}</div>
                                </div>
                            @else
                                <div>
                                    <span class="text-sm text-gray-900">Activity</span>
                                    <div class="text-xs text-orange-600">{{ $payment->activityBooking->activity->name }}</div>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-{{ $payment->payment_method === 'cash' ? 'money-bill' : ($payment->payment_method === 'card' ? 'credit-card' : ($payment->payment_method === 'bank_transfer' ? 'university' : 'globe')) }} mr-2"></i>
                                {{ ucfirst($payment->payment_method) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($payment->status == 'completed') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($payment->status == 'pending')
                                    <form method="POST" action="{{ route('admin.payments.complete', $payment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Complete">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.payments.edit', $payment) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                
                                @if($payment->status == 'completed')
                                    <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-900" title="Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if(in_array($payment->status, ['pending', 'failed']))
                                    <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.payments.generate-invoice', $payment) }}" class="text-purple-600 hover:text-purple-900" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No payments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
        <div class="flex-1 flex justify-between sm:hidden">
            {{ $payments->links() }}
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing 
                    <span class="font-medium">{{ $payments->firstItem() }}</span>
                    to 
                    <span class="font-medium">{{ $payments->lastItem() }}</span>
                    of 
                    <span class="font-medium">{{ $payments->total() }}</span>
                    results
                </p>
            </div>
            <div>
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
