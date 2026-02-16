<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Messages - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-indigo-600 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-white">HostelHub Admin</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-indigo-100">Administrator</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-100 hover:text-white bg-indigo-700 px-3 py-1 rounded">
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
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        Hostels
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                        Bookings
                    </a>
                    <a href="{{ route('admin.service-requests') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tools w-5 h-5 mr-3"></i>
                        Service Requests
                    </a>
                    <a href="{{ route('admin.service-providers') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-user-cog w-5 h-5 mr-3"></i>
                        Service Providers
                    </a>
                    <a href="{{ route('admin.messages') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <i class="fas fa-comments w-5 h-5 mr-3"></i>
                        Messages
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                        Analytics
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-cog w-5 h-5 mr-3"></i>
                        Settings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Admin Messages</h1>
                        <p class="text-gray-600 mt-2">Communicate with users and send broadcast messages</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                            <i class="fas fa-users mr-1"></i>
                            {{ $stats['total_users'] }} total users
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
                            <p class="text-sm text-gray-600">Students</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-home text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_landlords'] }}</p>
                            <p class="text-sm text-gray-600">Landlords</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-tools text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_service_providers'] }}</p>
                            <p class="text-sm text-gray-600">Service Providers</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <i class="fas fa-comment text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $conversations->count() }}</p>
                            <p class="text-sm text-gray-600">Conversations</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Broadcast Message -->
                <div class="lg:col-span-1">
                    <!-- Broadcast Message Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-bullhorn mr-2 text-indigo-600"></i>
                                Broadcast Message
                            </h3>
                        </div>
                        <div class="p-6">
                            <form id="broadcastForm">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Send to User Types
                                        </label>
                                        <div class="space-y-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="user_types[]" value="student" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                                <span class="ml-2 text-sm text-gray-700">
                                                    <i class="fas fa-user-graduate mr-1 text-blue-500"></i>
                                                    Students ({{ $stats['total_students'] }})
                                                </span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="user_types[]" value="landlord" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                                <span class="ml-2 text-sm text-gray-700">
                                                    <i class="fas fa-home mr-1 text-green-500"></i>
                                                    Landlords ({{ $stats['total_landlords'] }})
                                                </span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" name="user_types[]" value="service_provider" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                                                <span class="ml-2 text-sm text-gray-700">
                                                    <i class="fas fa-tools mr-1 text-purple-500"></i>
                                                    Service Providers ({{ $stats['total_service_providers'] }})
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="broadcast_message" class="block text-sm font-medium text-gray-700 mb-2">
                                            Message
                                        </label>
                                        <textarea
                                            id="broadcast_message"
                                            name="message"
                                            rows="6"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                            placeholder="Type your broadcast message here..."
                                            maxlength="1000"
                                            required
                                        ></textarea>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs text-gray-500">Max 1000 characters</span>
                                            <span id="charCount" class="text-xs text-gray-500">0/1000</span>
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        id="broadcastBtn"
                                        class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 transition duration-150 font-medium flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    >
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Send Broadcast Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Send to User -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-user-plus mr-2 text-green-600"></i>
                                Quick Message
                            </h3>
                        </div>
                        <div class="p-6">
                            <form id="quickMessageForm">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="quick_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Select User
                                        </label>
                                        <select
                                            id="quick_user_id"
                                            name="user_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            required
                                        >
                                            <option value="">Choose a user...</option>
                                            @foreach($users as $userType => $userGroup)
                                                <optgroup label="{{ ucfirst($userType) }}s">
                                                    @foreach($userGroup as $user)
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="quick_message" class="block text-sm font-medium text-gray-700 mb-2">
                                            Message
                                        </label>
                                        <textarea
                                            id="quick_message"
                                            name="message"
                                            rows="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                            placeholder="Type your message here..."
                                            maxlength="1000"
                                            required
                                        ></textarea>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-xs text-gray-500">Max 1000 characters</span>
                                            <span id="quickCharCount" class="text-xs text-gray-500">0/1000</span>
                                        </div>
                                    </div>

                                    <button
                                        type="submit"
                                        id="quickMessageBtn"
                                        class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition duration-150 font-medium flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    >
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Conversations -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-comments mr-2 text-gray-600"></i>
                                    Recent Conversations
                                </h3>
                                <span class="text-sm text-gray-500">
                                    {{ $conversations->count() }} conversations
                                </span>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                            @forelse($conversations as $conversation)
                                @php
                                    $otherUser = $conversation->user1_id == auth()->id() ? $conversation->user2 : $conversation->user1;
                                    $lastMessage = $conversation->lastMessage;
                                    $isActive = request('conversation_id') == $conversation->id;
                                @endphp

                                <a href="{{ route('admin.messages', ['conversation_id' => $conversation->id]) }}"
                                   class="block p-4 hover:bg-gray-50 transition duration-150 {{ $isActive ? 'bg-indigo-50 border-r-4 border-indigo-500' : '' }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $otherUser->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 capitalize">
                                                        <i class="fas
                                                            {{ $otherUser->user_type == 'student' ? 'fa-user-graduate text-blue-500' : '' }}
                                                            {{ $otherUser->user_type == 'landlord' ? 'fa-home text-green-500' : '' }}
                                                            {{ $otherUser->user_type == 'service_provider' ? 'fa-tools text-purple-500' : '' }}
                                                            mr-1"
                                                        ></i>
                                                        {{ $otherUser->user_type }}
                                                    </p>
                                                </div>
                                                @if($conversation->unread_count > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($lastMessage)
                                                <p class="text-sm text-gray-600 mt-1 truncate">
                                                    {{ $lastMessage->message }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $lastMessage->created_at->diffForHumans() }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-400 mt-1">No messages yet</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No conversations yet</p>
                                    <p class="text-gray-400 text-sm mt-1">Start a conversation by sending a message</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Selected Conversation -->
                    @if($selectedConversation)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $otherUser = $selectedConversation->user1_id == auth()->id() ? $selectedConversation->user2 : $selectedConversation->user1;
                                        @endphp
                                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $otherUser->name }}</p>
                                            <p class="text-xs text-gray-500 capitalize">{{ $otherUser->user_type }}</p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $otherUser->email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="p-4 max-h-96 overflow-y-auto space-y-4" id="messagesContainer">
                                @foreach($selectedConversation->messages->sortBy('created_at') as $message)
                                    <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg
                                            {{ $message->sender_id == auth()->id()
                                                ? 'bg-indigo-600 text-white rounded-br-none'
                                                : 'bg-gray-200 text-gray-900 rounded-bl-none' }}">
                                            <p class="text-sm">{{ $message->message }}</p>
                                            <p class="text-xs mt-1 opacity-75">
                                                {{ $message->created_at->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reply Form -->
                            <div class="p-4 border-t border-gray-200">
                                <form id="replyForm" class="flex space-x-2">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $otherUser->id }}">
                                    <input type="text"
                                           name="message"
                                           id="replyMessage"
                                           placeholder="Type your message..."
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           maxlength="1000"
                                           required>
                                    <button type="submit"
                                            id="replyBtn"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-150 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast -->
    <div id="toast" class="fixed top-4 right-4 p-4 rounded-lg shadow-lg hidden z-50">
        <div class="flex items-center">
            <i id="toastIcon" class="mr-3"></i>
            <span id="toastMessage" class="text-sm font-medium"></span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const broadcastMessage = document.getElementById('broadcast_message');
            const quickMessage = document.getElementById('quick_message');
            const replyMessage = document.getElementById('replyMessage');
            const charCount = document.getElementById('charCount');
            const quickCharCount = document.getElementById('quickCharCount');

            function updateCharCount(textarea, counter) {
                const length = textarea.value.length;
                counter.textContent = `${length}/1000`;

                // Change color when approaching limit
                if (length > 900) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-gray-500');
                } else {
                    counter.classList.remove('text-red-500');
                    counter.classList.add('text-gray-500');
                }
            }

            // Initialize character counters
            broadcastMessage.addEventListener('input', () => updateCharCount(broadcastMessage, charCount));
            quickMessage.addEventListener('input', () => updateCharCount(quickMessage, quickCharCount));
            if (replyMessage) {
                replyMessage.addEventListener('input', function() {
                    const replyBtn = document.getElementById('replyBtn');
                    replyBtn.disabled = !this.value.trim();
                });
            }

            // Update button states based on form validity
            function updateButtonStates() {
                const broadcastBtn = document.getElementById('broadcastBtn');
                const quickMessageBtn = document.getElementById('quickMessageBtn');

                // Broadcast button - require message and at least one user type
                const hasUserTypes = document.querySelectorAll('input[name="user_types[]"]:checked').length > 0;
                broadcastBtn.disabled = !broadcastMessage.value.trim() || !hasUserTypes;

                // Quick message button - require user selection and message
                const quickUserId = document.getElementById('quick_user_id').value;
                quickMessageBtn.disabled = !quickUserId || !quickMessage.value.trim();
            }

            // Add event listeners for form validation
            broadcastMessage.addEventListener('input', updateButtonStates);
            quickMessage.addEventListener('input', updateButtonStates);
            document.getElementById('quick_user_id').addEventListener('change', updateButtonStates);
            document.querySelectorAll('input[name="user_types[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateButtonStates);
            });

            // Initialize button states
            updateButtonStates();

            // Broadcast form submission
            document.getElementById('broadcastForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const button = document.getElementById('broadcastBtn');
                const originalText = button.innerHTML;

                // Validate message content
                const message = formData.get('message').trim();
                if (!message) {
                    showToast('error', 'Please enter a message');
                    return;
                }

                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

                try {
                    const response = await fetch('{{ route("admin.messages.broadcast") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('success', data.message || 'Broadcast message sent successfully!');
                        this.reset();
                        updateCharCount(broadcastMessage, charCount);
                        updateButtonStates();
                    } else {
                        showToast('error', data.message || 'Failed to send broadcast message');
                    }
                } catch (error) {
                    console.error('Broadcast error:', error);
                    showToast('error', 'Network error. Please try again.');
                } finally {
                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Broadcast Message';
                    updateButtonStates();
                }
            });

            // Quick message form submission
            document.getElementById('quickMessageForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const userId = document.getElementById('quick_user_id').value;
                const message = document.getElementById('quick_message').value.trim();

                if (!userId) {
                    showToast('error', 'Please select a user');
                    return;
                }

                if (!message) {
                    showToast('error', 'Please enter a message');
                    return;
                }

                const formData = new FormData(this);
                const button = document.getElementById('quickMessageBtn');
                const originalText = button.innerHTML;

                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

                try {
                    const response = await fetch('{{ route("admin.messages.send") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('success', data.message || 'Message sent successfully!');
                        this.reset();
                        updateCharCount(quickMessage, quickCharCount);
                        updateButtonStates();

                        // Redirect to conversation if needed
                        if (data.conversation_id && !window.location.href.includes('conversation_id')) {
                            window.location.href = '{{ route("admin.messages") }}?conversation_id=' + data.conversation_id;
                        }
                    } else {
                        showToast('error', data.message || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Quick message error:', error);
                    showToast('error', 'Network error. Please try again.');
                } finally {
                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Message';
                    updateButtonStates();
                }
            });

            // Reply form submission (if exists)
            const replyForm = document.getElementById('replyForm');
            if (replyForm) {
                replyForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const input = this.querySelector('input[name="message"]');
                    const button = document.getElementById('replyBtn');
                    const message = input.value.trim();

                    if (!message) {
                        showToast('error', 'Please enter a message');
                        return;
                    }

                    const originalText = button.innerHTML;

                    // Disable button and show loading
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    try {
                        const response = await fetch('{{ route("admin.messages.send") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            input.value = '';
                            button.disabled = true;

                            // Reload the page to show new message
                            window.location.reload();
                        } else {
                            showToast('error', data.message || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('Reply error:', error);
                        showToast('error', 'Network error. Please try again.');
                    } finally {
                        // Re-enable button
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    }
                });
            }

            // Toast function
            function showToast(type, message) {
                const toast = document.getElementById('toast');
                const toastIcon = document.getElementById('toastIcon');
                const toastMessage = document.getElementById('toastMessage');

                // Set styles based on type
                if (type === 'success') {
                    toast.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 bg-green-500 text-white';
                    toastIcon.className = 'fas fa-check-circle';
                } else {
                    toast.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 bg-red-500 text-white';
                    toastIcon.className = 'fas fa-exclamation-circle';
                }

                toastMessage.textContent = message;
                toast.classList.remove('hidden');

                // Auto hide after 5 seconds
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 5000);
            }

            // Auto-scroll to bottom of messages container
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>
