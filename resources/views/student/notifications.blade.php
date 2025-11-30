<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Notifications - Hostel Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.dashboard') }}"
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Notifications Page -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                        <p class="text-gray-600">Manage your notifications</p>
                    </div>
                    @if($notifications->count() > 0)
                        <div class="flex space-x-2">
                            <button onclick="markAllAsRead()"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                                Mark All as Read
                            </button>
                            <button onclick="clearAllNotifications()"
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-medium">
                                Clear All
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notifications List -->
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150
                               {{ !$notification->is_read ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}"
                         id="notification-{{ $notification->id }}">
                        <div class="flex justify-between items-start">
                            <div class="flex space-x-4 flex-1">
                                <!-- Icon -->
                                <div class="flex-shrink-0 text-xl mt-1">
                                    {!! $notification->getIcon() !!}
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="text-gray-600 mt-1">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                                        ({{ $notification->created_at->diffForHumans() }})
                                    </p>

                                    @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}"
                                           class="inline-block mt-3 text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            View Details →
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col space-y-2 ml-4">
                                @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Mark Read
                                    </button>
                                @endif
                                <button onclick="deleteNotification({{ $notification->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                        <p class="text-gray-500">You're all caught up! New notifications will appear here.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationElement = document.getElementById(`notification-${notificationId}`);
                    notificationElement.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');

                    // Update the mark read button
                    const markReadBtn = notificationElement.querySelector('button:first-child');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAllAsRead() {
            if (!confirm('Mark all notifications as read?')) return;

            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove all unread styles
                    document.querySelectorAll('[id^="notification-"]').forEach(element => {
                        element.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');
                    });

                    // Remove all mark read buttons
                    document.querySelectorAll('button:first-child').forEach(button => {
                        if (button.textContent === 'Mark Read') {
                            button.remove();
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deleteNotification(notificationId) {
            if (!confirm('Are you sure you want to delete this notification?')) return;

            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`notification-${notificationId}`).remove();

                    // If no notifications left, show empty state
                    if (document.querySelectorAll('[id^="notification-"]').length === 0) {
                        location.reload();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function clearAllNotifications() {
            if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) return;

            fetch('/notifications/clear-all', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
