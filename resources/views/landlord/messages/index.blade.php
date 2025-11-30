<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - HostelHub Landlord</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub Landlord</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar and Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('landlord.dashboard') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('landlord.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        My Hostels
                    </a>
                    <a href="{{ route('landlord.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                    <a href="{{ route('landlord.messages') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Messages
                    </a>
                    <a href="{{ route('landlord.reviews') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Reviews
                    </a>
                    <a href="{{ route('landlord.earnings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Earnings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
                        <p class="text-gray-600 mt-2">Communicate with students and manage inquiries</p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="newMessageBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Message
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Messages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_messages'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Unread Messages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['unread_messages'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Conversations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_conversations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Layout -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex h-[600px]">
                    <!-- Conversations List -->
                    <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                        <div class="p-4 border-b border-gray-200">
                            <div class="relative">
                                <input type="text" id="searchConversations" placeholder="Search conversations..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Conversations -->
                        <div class="divide-y divide-gray-200" id="conversationsList">
                            @forelse($conversations as $conversation)
                                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer border-l-4
                                    {{ request('booking_id') == $conversation->id ? 'border-blue-500 bg-blue-50' : 'border-transparent' }}
                                    {{ $conversation->unread_count > 0 ? 'bg-blue-50' : '' }}"
                                    data-conversation-id="{{ $conversation->id }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold">
                                                    {{ substr($conversation->student->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <h4 class="font-semibold text-gray-900 truncate">
                                                    {{ $conversation->student->name }}
                                                </h4>
                                                <span class="text-xs text-gray-500">
                                                    {{ $conversation->last_message_time }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 truncate mt-1">
                                                {{ $conversation->last_message }}
                                            </p>
                                            <div class="flex justify-between items-center mt-2">
                                                <span class="text-xs text-gray-500">
                                                    {{ $conversation->hostel->name }}
                                                </span>
                                                @if($conversation->unread_count > 0)
                                                    <span class="bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <p class="text-gray-500">No conversations yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Messages from students will appear here</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Message Thread -->
                    <div class="flex-1 flex flex-col">
                        @if($selectedConversation)
                            <!-- Message Header -->
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">
                                                {{ substr($selectedConversation->student->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $selectedConversation->student->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $selectedConversation->hostel->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </button>
                                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="messagesContainer">
                                <div class="space-y-4">
                                    @foreach($selectedConversation->messages as $message)
                                        <div class="flex {{ $message->is_sender ? 'justify-end' : 'justify-start' }}">
                                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg
                                                {{ $message->is_sender ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-800' }}">
                                                <p class="text-sm">{{ $message->content }}</p>
                                                <p class="text-xs mt-1 {{ $message->is_sender ? 'text-blue-200' : 'text-gray-500' }} text-right">
                                                    {{ $message->created_at->format('h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="p-4 border-t border-gray-200">
                                <form id="messageForm" action="{{ route('landlord.messages.send') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $selectedConversation->id }}">
                                    <div class="flex space-x-3">
                                        <div class="flex-1">
                                            <textarea name="message" id="messageInput" rows="2"
                                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                                      placeholder="Type your message..."></textarea>
                                        </div>
                                        <button type="submit"
                                                class="self-end bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Send
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No conversation selected</h3>
                                    <p class="text-gray-500">Select a conversation from the list to start messaging</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select conversation functionality
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                item.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-conversation-id');

                    // Redirect to the same page with booking_id parameter
                    window.location.href = `/landlord/messages?booking_id=${bookingId}`;
                });
            });

            // Search conversations
            const searchInput = document.getElementById('searchConversations');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const conversations = document.querySelectorAll('.conversation-item');

                conversations.forEach(conversation => {
                    const studentName = conversation.querySelector('h4').textContent.toLowerCase();
                    const hostelName = conversation.querySelector('.text-xs.text-gray-500').textContent.toLowerCase();

                    if (studentName.includes(searchTerm) || hostelName.includes(searchTerm)) {
                        conversation.style.display = 'block';
                    } else {
                        conversation.style.display = 'none';
                    }
                });
            });

            // Send message form
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to show the new message
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            }
        });
    </script>
</body>
</html>
