<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .message-enter {
            opacity: 0;
            transform: translateY(10px);
        }
        .message-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 300ms, transform 300ms;
        }
        .conversation-highlight {
            animation: highlight 2s ease-in-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.1); }
            100% { background-color: transparent; }
        }
        .typing-indicator {
            display: inline-flex;
            align-items: center;
            background: #f3f4f6;
            padding: 8px 12px;
            border-radius: 18px;
            font-style: italic;
            color: #6b7280;
        }
        .typing-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #9CA3AF;
            margin: 0 1px;
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3">
                        <div class="bg-blue-600 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">HostelHub</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('student.my-bookings') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-calendar-check mr-2"></i>My Bookings
                    </a>
                    <a href="{{ route('student.services.index') }}" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-tools mr-2"></i>Services
                    </a>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-comments text-blue-600 mr-3"></i>
                            Messages
                        </h1>
                        <p class="text-gray-600 mt-2 flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            Chat with landlords and service providers
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="newConversationBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i>
                            New Chat
                        </button>
                       
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-comment text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Conversations</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalConversations">{{ count($conversations ?? []) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-orange-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-bell text-orange-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Unread Messages</p>
                            <p class="text-2xl font-bold text-gray-900" id="unreadCount">
                                {{ collect($conversations ?? [])->sum('unread_count') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-home text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Landlords</p>
                            <p class="text-2xl font-bold text-gray-900" id="landlordsCount">
                                {{ collect($conversations ?? [])->where('type', 'landlord')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-tools text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Service Providers</p>
                            <p class="text-2xl font-bold text-gray-900" id="providersCount">
                                {{ collect($conversations ?? [])->where('type', 'service_provider')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Layout -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex h-[70vh]">
                    <!-- Conversations Sidebar -->
                    <div class="w-1/3 border-r border-gray-200 flex flex-col">
                        <!-- Search and Filter Header -->
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <div class="relative mb-3">
                                <input type="text" id="searchConversations" placeholder="Search conversations..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <i class="fas fa-search text-gray-400 absolute left-3 top-3.5"></i>
                            </div>
                            <div class="flex space-x-2">
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-blue-500 hover:text-blue-600 transition-colors active" data-filter="all">
                                    All
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-blue-500 hover:text-blue-600 transition-colors" data-filter="unread">
                                    Unread
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-green-500 hover:text-green-600 transition-colors" data-filter="service_provider">
                                    Services
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-purple-500 hover:text-purple-600 transition-colors" data-filter="landlord">
                                    Landlords
                                </button>
                            </div>
                        </div>

                        <!-- Conversations List -->
                        <div class="flex-1 overflow-y-auto" id="conversationsList">
                            @forelse($conversations ?? [] as $conversation)
                                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer border-l-4 transition-all duration-200
                                    {{ request('conversation_id') == $conversation['id'] ? 'border-blue-500 bg-blue-50' : 'border-transparent' }}
                                    {{ $conversation['unread_count'] > 0 ? 'bg-blue-25 border-blue-300' : '' }}"
                                    data-conversation-id="{{ $conversation['id'] }}"
                                    data-conversation-type="{{ $conversation['type'] }}"
                                    data-user-name="{{ strtolower($conversation['other_user']->name) }}"
                                    data-last-message="{{ strtolower($conversation['last_message']) }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 relative">
                                            @if($conversation['type'] == 'service_provider')
                                                <div class="h-12 w-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-tools text-white text-sm"></i>
                                                </div>
                                            @else
                                                <div class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-home text-white text-sm"></i>
                                                </div>
                                            @endif
                                            @if($conversation['unread_count'] > 0)
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-sm">
                                                    {{ $conversation['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="font-semibold text-gray-900 truncate flex items-center">
                                                    {{ $conversation['other_user']->name }}
                                                    @if($conversation['type'] == 'service_provider')
                                                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Service</span>
                                                    @else
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Landlord</span>
                                                    @endif
                                                </h4>
                                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                                    {{ $conversation['last_message_time']->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 truncate mb-2">
                                                {{ $conversation['last_message'] }}
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-500 capitalize">
                                                    @if($conversation['type'] == 'service_provider')
                                                        <i class="fas fa-wrench mr-1"></i>Service Provider
                                                    @else
                                                        <i class="fas fa-user-tie mr-1"></i>Landlord
                                                    @endif
                                                </span>
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                        <i class="fas fa-envelope mr-1"></i>New
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-comments text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                                    <p class="text-gray-500 mb-4">Start a conversation by requesting a service or messaging a landlord.</p>
                                    <button id="emptyStateServiceBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto">
                                        <i class="fas fa-tools mr-2"></i>
                                        Request Service
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Message Thread -->
                    <div class="flex-1 flex flex-col">
                        @if(request('conversation_id'))
                            <!-- Message thread will be loaded dynamically -->
                            <div id="messageThread" class="flex-1 flex flex-col">
                                <!-- Loading state -->
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-3"></div>
                                        <p class="text-gray-500">Loading conversation...</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Welcome State -->
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center max-w-md">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 mb-6">
                                        <i class="fas fa-comments text-blue-400 text-5xl mb-4"></i>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Welcome to Messages</h3>
                                        <p class="text-gray-600 mb-4">Select a conversation to start messaging or request a service for assistance.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:border-blue-300 transition-colors cursor-pointer" onclick="showNewChatModal('landlord')">
                                            <i class="fas fa-home text-blue-500 text-2xl mb-2"></i>
                                            <p class="text-sm font-medium text-gray-900">Message Landlords</p>
                                            <p class="text-xs text-gray-500">About your bookings</p>
                                        </div>
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:border-green-300 transition-colors cursor-pointer" onclick="showNewChatModal('service_provider')">
                                            <i class="fas fa-tools text-green-500 text-2xl mb-2"></i>
                                            <p class="text-sm font-medium text-gray-900">Request Services</p>
                                            <p class="text-xs text-gray-500">Get help with issues</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div id="newChatModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-xl rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="newChatModalTitle">
                        Start New Conversation
                    </h3>
                    <button onclick="closeNewChatModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div id="landlordsSection" class="hidden">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-home text-blue-500 mr-2"></i>
                            Select Landlord
                        </h4>
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                            <div id="landlordsList" class="divide-y divide-gray-200">
                                <!-- Landlords will be loaded here -->
                            </div>
                            <div id="landlordsLoading" class="p-4 text-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                                <p class="text-gray-500 mt-2">Loading landlords...</p>
                            </div>
                            <div id="noLandlords" class="p-8 text-center hidden">
                                <i class="fas fa-home text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No landlords found</p>
                                <p class="text-sm text-gray-400 mt-1">Landlords from your bookings will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="serviceProvidersSection" class="hidden">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-tools text-green-500 mr-2"></i>
                            Select Service Provider
                        </h4>
                        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                            <div id="serviceProvidersList" class="divide-y divide-gray-200">
                                <!-- Service providers will be loaded here -->
                            </div>
                            <div id="serviceProvidersLoading" class="p-4 text-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div>
                                <p class="text-gray-500 mt-2">Loading service providers...</p>
                            </div>
                            <div id="noServiceProviders" class="p-8 text-center hidden">
                                <i class="fas fa-tools text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500">No service providers found</p>
                                <p class="text-sm text-gray-400 mt-1">Available service providers will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div id="newChatMessageSection" class="mt-4 hidden">
                    <div class="mb-4">
                        <label for="newChatMessage" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                        <textarea id="newChatMessage" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Type your initial message..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeNewChatModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="startNewChatBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Start Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

 

    <script>
        let currentConversationId = null;
        let currentConversationType = null;
        let selectedRecipient = null;
        let selectedRecipientType = null;
        let refreshInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeMessaging();
        });

        function initializeMessaging() {
            setupEventListeners();
            loadInitialConversation();
            startAutoRefresh();
        }

        function setupEventListeners() {
            // Conversation selection
            document.addEventListener('click', function(e) {
                const conversationItem = e.target.closest('.conversation-item');
                if (conversationItem) {
                    const conversationId = conversationItem.getAttribute('data-conversation-id');
                    const conversationType = conversationItem.getAttribute('data-conversation-type');
                    selectConversation(conversationId, conversationType, conversationItem);
                }
            });

            // Search and filter
            const searchInput = document.getElementById('searchConversations');
            searchInput.addEventListener('input', debounce(filterConversations, 300));

            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    applyFilter(filter);
                });
            });

            // New conversation button
            document.getElementById('newConversationBtn').addEventListener('click', function() {
                showNewChatModal('landlord');
            });

          
        }

        function selectConversation(conversationId, conversationType, element) {
            // Update UI
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('border-blue-500', 'bg-blue-50');
            });
            element.classList.add('border-blue-500', 'bg-blue-50', 'conversation-highlight');

            // Load conversation
            loadConversation(conversationId, conversationType);

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('conversation_id', conversationId);
            url.searchParams.set('type', conversationType);
            window.history.pushState({}, '', url);

            currentConversationId = conversationId;
            currentConversationType = conversationType;
        }

        function loadConversation(conversationId, conversationType) {
            const messageThread = document.getElementById('messageThread');
            if (!messageThread) {
                // Create message thread container if it doesn't exist
                const mainContent = document.querySelector('.flex-1.flex-col');
                const threadHTML = `
                    <div id="messageThread" class="flex-1 flex flex-col">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-3"></div>
                                <p class="text-gray-500">Loading conversation...</p>
                            </div>
                        </div>
                    </div>
                `;
                mainContent.innerHTML = threadHTML;
            } else {
                messageThread.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-3"></div>
                            <p class="text-gray-500">Loading conversation...</p>
                        </div>
                    </div>
                `;
            }

            fetch(`/student/conversations/${conversationId}/messages`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderMessageThread(data.messages, data.otherUser, conversationType, conversationId);
                    } else {
                        showError('Failed to load conversation: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error loading conversation:', error);
                    showError('Error loading messages. Please try again.');
                });
        }

        function renderMessageThread(messages, otherUser, conversationType, conversationId) {
            const messageThread = document.getElementById('messageThread');

            const userBadge = conversationType === 'service_provider' ?
                '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-2">Service Provider</span>' :
                '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-2">Landlord</span>';

            const userIcon = conversationType === 'service_provider' ?
                '<div class="h-10 w-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-sm"><i class="fas fa-tools text-white text-sm"></i></div>' :
                '<div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm"><i class="fas fa-home text-white text-sm"></i></div>';

            let messageHtml = `
                <!-- Message Header -->
                <div class="p-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            ${userIcon}
                            <div>
                                <h3 class="font-semibold text-gray-900 flex items-center">
                                    ${otherUser.name}
                                    ${userBadge}
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-circle text-xs mr-1 ${conversationType === 'service_provider' ? 'text-green-500' : 'text-blue-500'}"></i>
                                    ${conversationType === 'service_provider' ? 'Service Provider' : 'Landlord'}
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="Call">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="Info">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="messagesContainer">
                    <div class="space-y-4" id="messagesList">
            `;

            if (messages && messages.length > 0) {
                messages.forEach(message => {
                    const isOwnMessage = message.sender_id === {{ Auth::id() }};
                    const messageTime = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                    messageHtml += `
                        <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} message-item fade-in">
                            <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm message-bubble
                                ${isOwnMessage ?
                                    'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-none' :
                                    'bg-white border border-gray-200 text-gray-800 rounded-bl-none'}">
                                <p class="text-sm ${isOwnMessage ? 'text-white' : 'text-gray-800'}">${message.message}</p>
                                <p class="text-xs mt-2 ${isOwnMessage ? 'text-blue-200' : 'text-gray-500'} text-right">
                                    <i class="fas fa-clock mr-1"></i>${messageTime}
                                </p>
                            </div>
                        </div>
                    `;
                });
            } else {
                messageHtml += `
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No messages yet</p>
                        <p class="text-gray-400 text-sm">Start the conversation by sending a message</p>
                    </div>
                `;
            }

            messageHtml += `
                    </div>
                </div>

                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    <form id="messageForm" class="flex space-x-3">
                        @csrf
                        <input type="hidden" name="conversation_id" value="${conversationId}">
                        <input type="hidden" name="receiver_id" value="${otherUser.id}">
                        <div class="flex-1 relative">
                            <textarea name="message" id="messageInput" rows="1"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Type your message..."
                                oninput="autoResize(this)"></textarea>
                            <button type="button" class="absolute right-3 bottom-3 text-gray-400 hover:text-blue-600 transition-colors">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                        <button type="submit"
                                class="self-end bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send
                        </button>
                    </form>
                </div>
            `;

            messageThread.innerHTML = messageHtml;

            // Add message sending functionality
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendMessage(this);
                });
            }

            // Scroll to bottom
            scrollToBottom();
        }

        function sendMessage(form) {
            const formData = new FormData(form);
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message) {
                showError('Please enter a message');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Disable form and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            fetch('/student/messages/send', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    autoResize(messageInput);
                    // Reload conversation to show new message
                    loadConversation(currentConversationId, currentConversationType);
                    showSuccess('Message sent successfully!');
                } else {
                    showError('Failed to send message: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                showError('Error sending message. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send';
            });
        }

        // New Chat Modal Functions
        function showNewChatModal(type) {
            const modal = document.getElementById('newChatModal');
            const title = document.getElementById('newChatModalTitle');
            const landlordsSection = document.getElementById('landlordsSection');
            const serviceProvidersSection = document.getElementById('serviceProvidersSection');
            const messageSection = document.getElementById('newChatMessageSection');

            selectedRecipientType = type;
            messageSection.classList.add('hidden');

            if (type === 'landlord') {
                title.innerHTML = '<i class="fas fa-home text-blue-500 mr-2"></i>Message a Landlord';
                landlordsSection.classList.remove('hidden');
                serviceProvidersSection.classList.add('hidden');
                loadLandlords();
            } else {
                title.innerHTML = '<i class="fas fa-tools text-green-500 mr-2"></i>Message a Service Provider';
                landlordsSection.classList.add('hidden');
                serviceProvidersSection.classList.remove('hidden');
                loadServiceProviders();
            }

            modal.classList.remove('hidden');
        }

        function closeNewChatModal() {
            document.getElementById('newChatModal').classList.add('hidden');
            selectedRecipient = null;
            selectedRecipientType = null;
            document.getElementById('newChatMessage').value = '';
        }

        function loadLandlords() {
            const landlordsList = document.getElementById('landlordsList');
            const landlordsLoading = document.getElementById('landlordsLoading');
            const noLandlords = document.getElementById('noLandlords');

            landlordsList.innerHTML = '';
            landlordsLoading.classList.remove('hidden');
            noLandlords.classList.add('hidden');

            fetch('/student/get-landlords')
                .then(response => response.json())
                .then(data => {
                    landlordsLoading.classList.add('hidden');

                    if (data.success && data.landlords && data.landlords.length > 0) {
                        data.landlords.forEach(landlord => {
                            const landlordElement = document.createElement('div');
                            landlordElement.className = 'p-3 hover:bg-gray-50 cursor-pointer landlord-item';
                            landlordElement.setAttribute('data-landlord-id', landlord.id);
                            landlordElement.innerHTML = `
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold">${landlord.name.charAt(0)}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900">${landlord.name}</h5>
                                        <p class="text-sm text-gray-500">${landlord.email}</p>
                                        <p class="text-xs text-gray-400">${landlord.hostel_name || 'Landlord'}</p>
                                    </div>
                                </div>
                            `;

                            landlordElement.addEventListener('click', function() {
                                document.querySelectorAll('.landlord-item').forEach(item => {
                                    item.classList.remove('bg-blue-50', 'border-blue-200');
                                });
                                this.classList.add('bg-blue-50', 'border-blue-200', 'border');
                                selectedRecipient = landlord;
                                showMessageInput();
                            });

                            landlordsList.appendChild(landlordElement);
                        });
                    } else {
                        noLandlords.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading landlords:', error);
                    landlordsLoading.classList.add('hidden');
                    noLandlords.classList.remove('hidden');
                });
        }

        function loadServiceProviders() {
            const serviceProvidersList = document.getElementById('serviceProvidersList');
            const serviceProvidersLoading = document.getElementById('serviceProvidersLoading');
            const noServiceProviders = document.getElementById('noServiceProviders');

            serviceProvidersList.innerHTML = '';
            serviceProvidersLoading.classList.remove('hidden');
            noServiceProviders.classList.add('hidden');

            fetch('/student/get-service-providers')
                .then(response => response.json())
                .then(data => {
                    serviceProvidersLoading.classList.add('hidden');

                    if (data.success && data.service_providers && data.service_providers.length > 0) {
                        data.service_providers.forEach(provider => {
                            const providerElement = document.createElement('div');
                            providerElement.className = 'p-3 hover:bg-gray-50 cursor-pointer service-provider-item';
                            providerElement.setAttribute('data-provider-id', provider.id);
                            providerElement.innerHTML = `
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600 font-semibold">${provider.name.charAt(0)}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900">${provider.company_name || provider.name}</h5>
                                        <p class="text-sm text-gray-500">${provider.service_type_name || 'Service Provider'}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">⭐ ${provider.rating || 'N/A'}</span>
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">${provider.experience_years || 0} yrs exp</span>
                                        </div>
                                    </div>
                                </div>
                            `;

                            providerElement.addEventListener('click', function() {
                                document.querySelectorAll('.service-provider-item').forEach(item => {
                                    item.classList.remove('bg-green-50', 'border-green-200');
                                });
                                this.classList.add('bg-green-50', 'border-green-200', 'border');
                                selectedRecipient = provider;
                                showMessageInput();
                            });

                            serviceProvidersList.appendChild(providerElement);
                        });
                    } else {
                        noServiceProviders.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading service providers:', error);
                    serviceProvidersLoading.classList.add('hidden');
                    noServiceProviders.classList.remove('hidden');
                });
        }

        function showMessageInput() {
            document.getElementById('newChatMessageSection').classList.remove('hidden');
            document.getElementById('startNewChatBtn').onclick = startNewChat;
        }

        function startNewChat() {
            const message = document.getElementById('newChatMessage').value.trim();

            if (!selectedRecipient) {
                showError('Please select a recipient');
                return;
            }

            if (!message) {
                showError('Please enter a message');
                return;
            }

            const startChatBtn = document.getElementById('startNewChatBtn');
            startChatBtn.disabled = true;
            startChatBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            fetch('/student/messages/start-conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    receiver_id: selectedRecipient.id,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeNewChatModal();
                    showSuccess('Conversation started successfully!');
                    // Redirect to the new conversation
                    setTimeout(() => {
                        window.location.href = `/student/messages?conversation_id=${data.conversation_id}&type=${selectedRecipientType}`;
                    }, 1000);
                } else {
                    showError(data.message || 'Failed to start conversation');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error starting conversation');
            })
            .finally(() => {
                startChatBtn.disabled = false;
                startChatBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Start Chat';
            });
        }

   
        // Utility functions
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function filterConversations() {
            const searchTerm = this.value.toLowerCase();
            const filter = document.querySelector('.filter-btn.active')?.getAttribute('data-filter') || 'all';

            document.querySelectorAll('.conversation-item').forEach(item => {
                const userName = item.getAttribute('data-user-name');
                const lastMessage = item.getAttribute('data-last-message');
                const type = item.getAttribute('data-conversation-type');
                const hasUnread = item.querySelector('.bg-red-500');

                const matchesSearch = userName.includes(searchTerm) || lastMessage.includes(searchTerm);
                const matchesFilter = filter === 'all' ||
                                    (filter === 'unread' && hasUnread) ||
                                    (filter === type);

                item.style.display = matchesSearch && matchesFilter ? 'block' : 'none';
            });
        }

        function applyFilter(filter) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-filter') === filter);
                if (btn.getAttribute('data-filter') === filter) {
                    btn.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
                } else {
                    btn.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50');
                }
            });
            filterConversations.call(document.getElementById('searchConversations'));
        }

        function startAutoRefresh() {
            // Refresh every 30 seconds if a conversation is open
            refreshInterval = setInterval(() => {
                if (document.visibilityState === 'visible' && currentConversationId) {
                    // Could implement actual refresh logic here
                    console.log('Auto-refreshing conversation...');
                }
            }, 30000);
        }

        function loadInitialConversation() {
            @if(request('conversation_id'))
                const conversationId = '{{ request("conversation_id") }}';
                const conversationType = '{{ request("type", "landlord") }}';
                const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                if (conversationItem) {
                    selectConversation(conversationId, conversationType, conversationItem);
                } else {
                    // If conversation item not found in list, still try to load it
                    loadConversation(conversationId, conversationType);
                }
            @endif
        }

        function showSuccess(message) {
            // Create a temporary success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(successDiv);

            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }

        function showError(message) {
            // Create a temporary error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(errorDiv);

            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }

        // Make functions available globally
        window.autoResize = autoResize;
        window.showNewChatModal = showNewChatModal;
        window.closeNewChatModal = closeNewChatModal;
    </script>
</body>
</html>
