<div class="relative" x-data="{ open: false }">
    <!-- Notifications Button -->
    <button @click="open = !open"
            class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        <!-- Unread Count Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50 max-h-96 overflow-y-auto">

        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                @if($notifications->count() > 0)
                    <button onclick="markAllAsRead()"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors duration-150
                           {{ !$notification->is_read ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}"
                     id="notification-{{ $notification->id }}">
                    <div class="flex space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 text-lg">
                            {!! $notification->getIcon() !!}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification->title }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $notification->message }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col items-end space-y-1">
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                    Mark read
                                </button>
                            @endif
                            <button onclick="deleteNotification({{ $notification->id }})"
                                    class="text-xs text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($notifications->count() > 0)
            <div class="p-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('student.notifications') }}"
                   class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    function markAsRead(notificationId) {
        fetch(`/student/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationElement = document.getElementById(`notification-${notificationId}`);
                if (notificationElement) {
                    notificationElement.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');
                }
                updateUnreadCount();
            }
        });
    }

    function markAllAsRead() {
        fetch('/student/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('[id^="notification-"]').forEach(element => {
                    element.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-500');
                });
                updateUnreadCount();
            }
        });
    }

    function deleteNotification(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            fetch(`/student/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationElement = document.getElementById(`notification-${notificationId}`);
                    if (notificationElement) {
                        notificationElement.remove();
                    }
                    updateUnreadCount();
                }
            });
        }
    }

    function updateUnreadCount() {
        fetch('/student/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                // Update the unread count badge
                const badge = document.querySelector('.relative .bg-red-500');
                if (badge) {
                    if (data.unread_count === 0) {
                        badge.remove();
                    } else {
                        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    }
                }
            });
    }

    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        updateUnreadCount();
    }, 30000);
</script>
