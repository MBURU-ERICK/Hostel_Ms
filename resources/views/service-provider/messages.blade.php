<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - HostelHub Services</title>

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
            0% { background-color: rgba(34, 197, 94, 0.1); }
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
        .conversation-item {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .conversation-item:hover {
            background-color: #f9fafb;
        }
        .conversation-item.active {
            background-color: #f0fdf4;
            border-left-color: #10b981;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('service-provider.dashboard') }}" class="flex items-center space-x-3">
                        <div class="bg-green-600 p-2 rounded-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">HostelHub Services</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-sm text-gray-700 font-medium">{{ Auth::user()->serviceProviderDetail->company_name ?? Auth::user()->name }}</span>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
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

    <!-- Sidebar and Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen border-r border-gray-200">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('service-provider.dashboard') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('service-provider.requests') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-tools w-5 h-5 mr-3"></i>
                        Service Requests
                    </a>
                    <a href="{{ route('service-provider.messages') }}"
                       class="flex items-center px-4 py-3 text-green-700 bg-green-50 border-l-4 border-green-500 rounded-lg">
                        <i class="fas fa-comments w-5 h-5 mr-3"></i>
                        Messages
                        @if($unreadCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('service-provider.earnings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-dollar-sign w-5 h-5 mr-3"></i>
                        Earnings
                    </a>
                   
                    <a href="{{ route('service-provider.profile') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-user w-5 h-5 mr-3"></i>
                        Profile
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
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-comments text-green-600 mr-3"></i>
                            Messages
                        </h1>
                        <p class="text-gray-600 mt-2 flex items-center">
                            <i class="fas fa-info-circle text-green-400 mr-2"></i>
                            Communicate with students and landlords
                        </p>
                    </div>
                    <button id="newMessageBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        New Message
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-comment text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Conversations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $conversations->count() }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $unreadCount }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Students</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $conversations->where('other_user.user_type', 'student')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-tie text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Landlords</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $conversations->where('other_user.user_type', 'landlord')->count() }}
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
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                                <i class="fas fa-search text-gray-400 absolute left-3 top-3.5"></i>
                            </div>
                            <div class="flex space-x-2">
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-green-500 hover:text-green-600 transition-colors active" data-filter="all">
                                    All
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-green-500 hover:text-green-600 transition-colors" data-filter="unread">
                                    Unread
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-blue-500 hover:text-blue-600 transition-colors" data-filter="student">
                                    Students
                                </button>
                                <button class="filter-btn px-3 py-1.5 text-xs rounded-full border border-gray-300 hover:border-purple-500 hover:text-purple-600 transition-colors" data-filter="landlord">
                                    Landlords
                                </button>
                            </div>
                        </div>

                        <!-- Conversations List -->
                        <div class="flex-1 overflow-y-auto" id="conversationsList">
                            @forelse($conversations as $conversation)
                                @php
                                    $otherUser = $conversation->user1_id == Auth::id() ? $conversation->user2 : $conversation->user1;
                                    $lastMessage = $conversation->lastMessage;
                                    $lastMessageText = $lastMessage ? $lastMessage->message : 'No messages yet';
                                    $lastMessageTime = $lastMessage ? $lastMessage->created_at->diffForHumans() : $conversation->created_at->diffForHumans();
                                    $isActive = request('conversation_id') == $conversation->id;
                                @endphp
                                <div class="conversation-item p-4 border-l-4 transition-all duration-200 {{ $isActive ? 'active border-green-500 bg-green-50' : 'border-transparent' }} {{ $conversation->unread_count > 0 ? 'bg-blue-25' : '' }}"
                                    data-conversation-id="{{ $conversation->id }}"
                                    data-user-type="{{ $otherUser->user_type }}"
                                    data-user-name="{{ strtolower($otherUser->name) }}"
                                    data-last-message="{{ strtolower($lastMessageText) }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 relative">
                                            @if($otherUser->user_type == 'student')
                                                <div class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-user-graduate text-white text-sm"></i>
                                                </div>
                                            @else
                                                <div class="h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-user-tie text-white text-sm"></i>
                                                </div>
                                            @endif
                                            @if($conversation->unread_count > 0)
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-sm">
                                                    {{ $conversation->unread_count }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="font-semibold text-gray-900 truncate flex items-center">
                                                    {{ $otherUser->name }}
                                                    @if($otherUser->user_type == 'student')
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Student</span>
                                                    @else
                                                        <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">Landlord</span>
                                                    @endif
                                                </h4>
                                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                                    {{ $lastMessageTime }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 truncate mb-2">
                                                {{ $lastMessageText }}
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-500 capitalize">
                                                    @if($otherUser->user_type == 'student')
                                                        <i class="fas fa-user-graduate mr-1"></i>Student
                                                    @else
                                                        <i class="fas fa-user-tie mr-1"></i>Landlord
                                                    @endif
                                                </span>
                                                @if($conversation->unread_count > 0)
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
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
                                    <p class="text-gray-500 mb-4">Messages from students and landlords will appear here when they contact you.</p>
                                    <button onclick="showNewMessageModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center mx-auto">
                                        <i class="fas fa-plus mr-2"></i>
                                        Start Conversation
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Message Thread -->
                    <div class="flex-1 flex flex-col">
                        @if($selectedConversation)
                            @php
                                $otherUser = $selectedConversation->user1_id == Auth::id() ? $selectedConversation->user2 : $selectedConversation->user1;
                            @endphp
                            <div id="messageThread" class="flex-1 flex flex-col">
                                <!-- Message Header -->
                                <div class="p-4 border-b border-gray-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            @if($otherUser->user_type == 'student')
                                                <div class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-user-graduate text-white text-sm"></i>
                                                </div>
                                            @else
                                                <div class="h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-user-tie text-white text-sm"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h3 class="font-semibold text-gray-900 flex items-center">
                                                    {{ $otherUser->name }}
                                                    @if($otherUser->user_type == 'student')
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Student</span>
                                                    @else
                                                        <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">Landlord</span>
                                                    @endif
                                                </h3>
                                                <p class="text-sm text-gray-500 flex items-center">
                                                    <i class="fas fa-circle text-xs mr-1 {{ $otherUser->user_type == 'student' ? 'text-blue-500' : 'text-purple-500' }}"></i>
                                                    {{ ucfirst($otherUser->user_type) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="Contact Info">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages Container -->
                                <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="messagesContainer">
                                    <div class="space-y-4" id="messagesList">
                                        @if($selectedConversation->messages && $selectedConversation->messages->count() > 0)
                                            @foreach($selectedConversation->messages->sortBy('created_at') as $message)
                                                <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }} message-item">
                                                    <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm
                                                        {{ $message->sender_id == Auth::id() ?
                                                            'bg-gradient-to-br from-green-500 to-green-600 text-white rounded-br-none' :
                                                            'bg-white border border-gray-200 text-gray-800 rounded-bl-none' }}">
                                                        <p class="text-sm {{ $message->sender_id == Auth::id() ? 'text-white' : 'text-gray-800' }}">
                                                            {{ is_string($message->message) ? $message->message : ($message->message['message'] ?? 'Invalid message format') }}
                                                        </p>
                                                        <p class="text-xs mt-2 {{ $message->sender_id == Auth::id() ? 'text-green-200' : 'text-gray-500' }} text-right">
                                                            <i class="fas fa-clock mr-1"></i>{{ $message->created_at->format('h:i A') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-8">
                                                <i class="fas fa-comments text-gray-300 text-4xl mb-4"></i>
                                                <p class="text-gray-500">No messages yet. Start the conversation!</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Input -->
                                <div class="p-4 border-t border-gray-200 bg-white">
                                    <form id="messageForm" class="flex space-x-3">
                                        @csrf
                                        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                                        <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                                        <div class="flex-1 relative">
                                            <textarea name="message" id="messageInput" rows="1"
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"
                                                placeholder="Type your message..."
                                                oninput="autoResize(this)"></textarea>
                                            <button type="button" class="absolute right-3 bottom-3 text-gray-400 hover:text-green-600 transition-colors">
                                                <i class="fas fa-paperclip"></i>
                                            </button>
                                        </div>
                                        <button type="submit"
                                                class="self-end bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all">
                                            <i class="fas fa-paper-plane mr-2"></i>
                                            Send
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Welcome State -->
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center max-w-md">
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 mb-6">
                                        <i class="fas fa-comments text-green-400 text-5xl mb-4"></i>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Welcome to Messages</h3>
                                        <p class="text-gray-600 mb-4">Select a conversation to start messaging with students and landlords.</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:border-blue-300 transition-colors">
                                            <i class="fas fa-user-graduate text-blue-500 text-2xl mb-2"></i>
                                            <p class="text-sm font-medium text-gray-900">Student Chats</p>
                                            <p class="text-xs text-gray-500">Service requests & inquiries</p>
                                        </div>
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:border-purple-300 transition-colors">
                                            <i class="fas fa-user-tie text-purple-500 text-2xl mb-2"></i>
                                            <p class="text-sm font-medium text-gray-900">Landlord Chats</p>
                                            <p class="text-xs text-gray-500">Property maintenance & services</p>
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

    <!-- New Message Modal -->
    <div id="newMessageModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <!-- Modal content remains the same -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeMessaging();
        });

        function initializeMessaging() {
            setupConversationClickHandlers();
            setupEventListeners();
            setupNewMessageModal();
        }

        function setupConversationClickHandlers() {
            // Add click event listeners to all conversation items
            const conversationItems = document.querySelectorAll('.conversation-item');
            
            conversationItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const conversationId = this.getAttribute('data-conversation-id');
                    console.log('Conversation clicked:', conversationId);
                    
                    if (conversationId) {
                        selectConversation(conversationId, this);
                    }
                });
            });

            // Also make sure the entire conversation item area is clickable
            document.getElementById('conversationsList').addEventListener('click', function(e) {
                const conversationItem = e.target.closest('.conversation-item');
                if (conversationItem) {
                    e.preventDefault();
                    const conversationId = conversationItem.getAttribute('data-conversation-id');
                    console.log('Conversation list click:', conversationId);
                    selectConversation(conversationId, conversationItem);
                }
            });
        }

        function selectConversation(conversationId, element) {
            console.log('Selecting conversation:', conversationId);
            
            // Remove active class from all conversation items
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active', 'border-green-500', 'bg-green-50');
            });
            
            // Add active class to clicked conversation
            if (element) {
                element.classList.add('active', 'border-green-500', 'bg-green-50');
            }
            
            // Update URL and navigate to the conversation
            const baseUrl = window.location.origin + window.location.pathname;
            const newUrl = `${baseUrl}?conversation_id=${conversationId}`;
            
            console.log('Navigating to:', newUrl);
            window.location.href = newUrl;
        }

        function setupEventListeners() {
            // Search and filter
            const searchInput = document.getElementById('searchConversations');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(filterConversations, 300));
            }

            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    applyFilter(filter);
                });
            });

            // Message form submission
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendMessage(this);
                });
            }
        }

        function sendMessage(form) {
            const formData = new FormData(form);
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message) return;

            // Disable form
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            fetch('{{ route("service-provider.messages.send") }}', {
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
                    messageInput.value = '';
                    autoResize(messageInput);
                    // Reload the page to show new message
                    window.location.reload();
                } else {
                    showError('Failed to send message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error sending message');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send';
            });
        }

        function setupNewMessageModal() {
            const newMessageBtn = document.getElementById('newMessageBtn');
            if (newMessageBtn) {
                newMessageBtn.addEventListener('click', showNewMessageModal);
            }

            // ... rest of new message modal setup
        }

        function showNewMessageModal() {
            document.getElementById('newMessageModal').classList.remove('hidden');
            resetNewMessageModal();
        }

        function closeNewMessageModal() {
            document.getElementById('newMessageModal').classList.add('hidden');
            resetNewMessageModal();
        }

        function resetNewMessageModal() {
            // Reset modal state
            document.getElementById('studentsSection').classList.add('hidden');
            document.getElementById('landlordsSection').classList.add('hidden');
            document.getElementById('messageInputSection').classList.add('hidden');
            
            // Reset selection styles
            document.getElementById('selectStudentsBtn').classList.remove('border-blue-500', 'bg-blue-100');
            document.getElementById('selectLandlordsBtn').classList.remove('border-purple-500', 'bg-purple-100');
            
            // Clear inputs
            document.getElementById('newMessageText').value = '';
        }

        // Utility functions
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
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
                const userType = item.getAttribute('data-user-type');
                const hasUnread = item.querySelector('.bg-red-500');

                const matchesSearch = userName.includes(searchTerm) || lastMessage.includes(searchTerm);
                const matchesFilter = filter === 'all' ||
                                    (filter === 'unread' && hasUnread) ||
                                    (filter === userType);

                item.style.display = matchesSearch && matchesFilter ? 'block' : 'none';
            });
        }

        function applyFilter(filter) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-filter') === filter);
                if (btn.getAttribute('data-filter') === filter) {
                    btn.classList.add('border-green-500', 'text-green-600', 'bg-green-50');
                } else {
                    btn.classList.remove('border-green-500', 'text-green-600', 'bg-green-50');
                }
            });
            filterConversations.call(document.getElementById('searchConversations'));
        }

        function showError(message) {
            alert(message);
        }

        // Make functions available globally
        window.autoResize = autoResize;
        window.showNewMessageModal = showNewMessageModal;
        window.closeNewMessageModal = closeNewMessageModal;
    </script>
</body>
</html>