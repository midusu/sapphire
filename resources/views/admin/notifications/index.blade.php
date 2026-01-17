@extends('admin.layout')

@section('title', 'Notifications - Sapphire Hotel Management')
@section('header', 'Notifications')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
        <p class="text-gray-600">View and manage your notifications</p>
    </div>
    @if($notifications->whereNull('read_at')->count() > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check-double mr-2"></i>Mark All as Read
            </button>
        </form>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <tr id="notification-row-{{ $notification->id }}" 
                        class="transition-colors duration-200 {{ $isUnread ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-white border-l-4 border-gray-300' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                @if($isUnread)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                @endif
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($notification->type == 'booking_confirmation') bg-green-100 text-green-800
                                    @elseif($notification->type == 'payment_alert') bg-blue-100 text-blue-800
                                    @elseif($notification->type == 'activity_reminder') bg-purple-100 text-purple-800
                                    @elseif($notification->type == 'housekeeping_alert') bg-yellow-100 text-yellow-800
                                    @elseif($notification->type == 'low_inventory') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium {{ $isUnread ? 'text-gray-900 font-bold' : 'text-gray-600' }}">
                                {{ $notification->title }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm {{ $isUnread ? 'text-gray-700' : 'text-gray-500' }}">
                                {{ Str::limit($notification->message, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $notification->created_at->format('M d, Y H:i') }}
                            <div class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isUnread)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-500 text-white animate-pulse">
                                    <i class="fas fa-circle mr-1"></i>Unread
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
                                    <i class="fas fa-check-circle mr-1"></i>Read
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($isUnread)
                                <form method="POST" 
                                      action="{{ route('admin.notifications.read', $notification->id) }}" 
                                      class="inline mark-as-read-form"
                                      data-notification-id="{{ $notification->id }}">
                                    @csrf
                                    <button type="submit" 
                                            class="text-blue-600 hover:text-blue-900 font-medium px-3 py-1 rounded hover:bg-blue-50 transition">
                                        <i class="fas fa-check mr-1"></i> Mark Read
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm">
                                    <i class="fas fa-check-circle mr-1"></i>Read
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <div class="py-8">
                                <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium">No notifications found.</p>
                                <p class="text-sm text-gray-500 mt-2">You're all caught up!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-3 border-t">
        {{ $notifications->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mark as read with AJAX
    document.querySelectorAll('.mark-as-read-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const notificationId = form.dataset.notificationId;
            const row = document.getElementById('notification-row-' + notificationId);
            const button = form.querySelector('button');
            
            // Disable button
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Marking...';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                              form.querySelector('input[name="_token"]')?.value;
            
            // Create form data
            const formData = new FormData();
            formData.append('_token', csrfToken);
            
            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update row appearance
                    row.classList.remove('bg-blue-50', 'border-blue-500');
                    row.classList.add('bg-white', 'border-gray-300');
                    
                    // Update status badge
                    const statusCell = row.querySelector('td:nth-child(5)');
                    statusCell.innerHTML = '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700"><i class="fas fa-check-circle mr-1"></i>Read</span>';
                    
                    // Update title and message colors
                    const titleCell = row.querySelector('td:nth-child(2) div');
                    titleCell.classList.remove('text-gray-900', 'font-bold');
                    titleCell.classList.add('text-gray-600');
                    
                    const messageCell = row.querySelector('td:nth-child(3) div');
                    messageCell.classList.remove('text-gray-700');
                    messageCell.classList.add('text-gray-500');
                    
                    // Update type indicator dot
                    const dot = row.querySelector('td:nth-child(1) .w-2');
                    if (dot) {
                        dot.classList.remove('bg-blue-500');
                        dot.classList.add('bg-gray-400');
                    }
                    
                    // Remove the mark as read button
                    const actionCell = row.querySelector('td:nth-child(6)');
                    actionCell.innerHTML = '<span class="text-gray-400 text-sm"><i class="fas fa-check-circle mr-1"></i>Read</span>';
                    
                    // Update unread count in header if exists
                    const bellBadge = document.querySelector('header .bg-red-500');
                    if (bellBadge) {
                        const currentCount = parseInt(bellBadge.textContent.trim());
                        if (currentCount > 1) {
                            bellBadge.textContent = currentCount - 1;
                        } else {
                            bellBadge.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check mr-1"></i> Mark Read';
                alert('Failed to mark notification as read. Please try again.');
            });
        });
    });
});
</script>
@endpush
@endsection