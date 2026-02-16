<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - HostelHub Landlord</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
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
        .conversation-highlight {
            animation: highlight 2s ease-in-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.1); }
            100% { background-color: transparent; }
        }
    </style>
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
                    <a href="{{ route('landlord.messages.index') }}"
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
                        <p class="text-gray-600 mt-2">Communicate with students and service providers</p>
                    </div>
                    <div class="flex space-x-3">
                        <button id="newMessageBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
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
                                @php
                                    $otherUser = $conversation->other_user;
                                    $userType = $otherUser->user_type ?? 'unknown';
                                    $userTypeColors = [
                                        'student' => 'blue',
                                        'service_provider' => 'green',
                                        'landlord' => 'purple',
                                        'admin' => 'red'
                                    ];
                                    $color = $userTypeColors[$userType] ?? 'gray';
                                @endphp

                                <div class="conversation-item p-4 hover:bg-gray-50 cursor-pointer border-l-4 transition-colors
                                    {{ request('conversation_id') == $conversation->id ? "border-{$color}-500 bg-{$color}-50" : 'border-transparent' }}
                                    {{ $conversation->unread_count > 0 ? "bg-{$color}-25" : '' }}"
                                    data-conversation-id="{{ $conversation->id }}"
                                    data-user-type="{{ $userType }}"
                                    data-user-name="{{ strtolower($otherUser->name ?? '') }}">

                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 bg-{{ $color }}-100 rounded-full flex items-center justify-center">
                                                <span class="text-{{ $color }}-600 font-semibold">
                                                    {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <h4 class="font-semibold text-gray-900 truncate">
                                                    {{ $otherUser->name ?? 'Unknown User' }}
                                                </h4>
                                                <span class="text-xs text-gray-500">
                                                    {{ optional($conversation->last_message_at)->diffForHumans() ?? '' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs bg-{{ $color }}-100 text-{{ $color }}-800 px-2 py-1 rounded capitalize">
                                                    {{ $userType }}
                                                </span>
                                                @if($conversation->unread_count > 0)
                                                    <span class="bg-{{ $color }}-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center ml-auto">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-sm text-gray-600 truncate mt-1">
                                                {{ optional($conversation->lastMessage)->message ?? 'No messages yet' }}
                                            </p>

                                            <div class="flex justify-between items-center mt-2">
                                                <span class="text-xs text-gray-500">
                                                    @if($conversation->booking)
                                                        {{ $conversation->booking->hostel->name ?? '—' }}
                                                    @elseif($conversation->serviceRequest)
                                                        Service Request
                                                    @else
                                                        Direct Message
                                                    @endif
                                                </span>
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
                                    <p class="text-sm text-gray-400 mt-1">Start a conversation to begin messaging</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Message Thread -->
                    <div class="flex-1 flex flex-col">
                        @if($selectedConversation)
                            @php
                                $otherUser = $selectedConversation->other_user;
                                $userType = $otherUser->user_type ?? 'unknown';
                                $userTypeColors = [
                                    'student' => 'blue',
                                    'service_provider' => 'green',
                                    'landlord' => 'purple',
                                    'admin' => 'red'
                                ];
                                $color = $userTypeColors[$userType] ?? 'gray';
                            @endphp

                            <!-- Message Header -->
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 bg-{{ $color }}-100 rounded-full flex items-center justify-center">
                                            <span class="text-{{ $color }}-600 font-semibold">
                                                {{ substr($otherUser->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm text-gray-500 capitalize">{{ $userType }}</span>
                                                @if($selectedConversation->booking)
                                                    <span class="text-sm text-gray-500">• {{ $selectedConversation->booking->hostel->name ?? '—' }}</span>
                                                @elseif($selectedConversation->serviceRequest)
                                                    <span class="text-sm text-gray-500">• Service Request</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="messagesContainer">
                                <div class="space-y-4" id="messagesList">
                                    @foreach($selectedConversation->messages as $message)
                                        @php
                                            $isSender = $message->sender_id === auth()->id();
                                        @endphp

                                        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }} message-item fade-in">
                                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg message-bubble
                                                {{ $isSender ? "bg-{$color}-600 text-white" : 'bg-white border border-gray-200 text-gray-800' }}">
                                                <p class="text-sm">{{ $message->message }}</p>
                                                <p class="text-xs mt-1 {{ $isSender ? "text-{$color}-200" : 'text-gray-500' }} text-right">
                                                    {{ $message->created_at->format('h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="p-4 border-t border-gray-200">
                                <form id="messageForm" class="flex space-x-3">
                                    @csrf
                                    <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                                    <div class="flex-1">
                                        <textarea name="message" id="messageInput" rows="2"
                                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-{{ $color }}-500 focus:border-{{ $color }}-500 resize-none"
                                                  placeholder="Type your message..."></textarea>
                                    </div>
                                    <button type="submit"
                                            class="self-end bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Send
                                    </button>
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

    <!-- New Message Modal -->
    <div id="newMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Start New Conversation</h3>
                    <button id="closeNewMessageModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Recipient Type Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Choose Recipient Type</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" id="selectStudentsBtn" class="p-4 border-2 border-blue-200 rounded-lg hover:border-blue-500 focus:border-blue-500 bg-blue-50 transition-colors">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-semibold text-blue-700">Students</span>
                                <p class="text-sm text-blue-600 mt-1">Message students who booked your hostels</p>
                            </div>
                        </button>
                        <button type="button" id="selectServiceProvidersBtn" class="p-4 border-2 border-green-200 rounded-lg hover:border-green-500 focus:border-green-500 bg-green-50 transition-colors">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-semibold text-green-700">Service Providers</span>
                                <p class="text-sm text-green-600 mt-1">Message available service providers</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Students Section -->
                <div id="studentsSection" class="hidden">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Select Student</h4>
                        <div class="relative">
                            <input type="text" id="searchStudents" placeholder="Search students..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                        <div id="studentsList" class="divide-y divide-gray-200">
                            <!-- Students will be loaded here -->
                        </div>
                        <div id="studentsLoading" class="p-4 text-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-500 mt-2">Loading students...</p>
                        </div>
                        <div id="noStudents" class="p-8 text-center hidden">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="text-gray-500">No students found</p>
                            <p class="text-sm text-gray-400 mt-1">Students who booked your hostels will appear here</p>
                        </div>
                    </div>
                </div>

                <!-- Service Providers Section -->
                <div id="serviceProvidersSection" class="hidden">
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Select Service Provider</h4>
                        <div class="relative">
                            <input type="text" id="searchServiceProviders" placeholder="Search service providers..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                        <div id="serviceProvidersList" class="divide-y divide-gray-200">
                            <!-- Service providers will be loaded here -->
                        </div>
                        <div id="serviceProvidersLoading" class="p-4 text-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div>
                            <p class="text-gray-500 mt-2">Loading service providers...</p>
                        </div>
                        <div id="noServiceProviders" class="p-8 text-center hidden">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-500">No service providers found</p>
                            <p class="text-sm text-gray-400 mt-1">Available service providers will appear here</p>
                        </div>
                    </div>
                </div>

                <!-- Message Input Section -->
                <div id="messageInputSection" class="hidden mt-6">
                    <div class="mb-4">
                        <label for="newMessageText" class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                        <textarea id="newMessageText" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Type your message..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelNewMessage" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="sendNewMessage" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const newMessageBtn = document.getElementById('newMessageBtn');
        const newMessageModal = document.getElementById('newMessageModal');
        const closeNewMessageModal = document.getElementById('closeNewMessageModal');
        const cancelNewMessage = document.getElementById('cancelNewMessage');
        const selectStudentsBtn = document.getElementById('selectStudentsBtn');
        const selectServiceProvidersBtn = document.getElementById('selectServiceProvidersBtn');
        const studentsSection = document.getElementById('studentsSection');
        const serviceProvidersSection = document.getElementById('serviceProvidersSection');
        const messageInputSection = document.getElementById('messageInputSection');
        const sendNewMessage = document.getElementById('sendNewMessage');

        let selectedRecipient = null;
        let selectedRecipientType = null;

        // Modal controls
        newMessageBtn.addEventListener('click', function() {
            newMessageModal.classList.remove('hidden');
            resetModal();
        });

        closeNewMessageModal.addEventListener('click', function() {
            newMessageModal.classList.add('hidden');
            resetModal();
        });

        cancelNewMessage.addEventListener('click', function() {
            newMessageModal.classList.add('hidden');
            resetModal();
        });

        // Recipient type selection
        selectStudentsBtn.addEventListener('click', function() {
            selectedRecipientType = 'student';
            showSection(studentsSection);
            loadStudents();
            resetSelection();
            selectStudentsBtn.classList.add('border-blue-500', 'bg-blue-100');
        });

        selectServiceProvidersBtn.addEventListener('click', function() {
            selectedRecipientType = 'service_provider';
            showSection(serviceProvidersSection);
            loadServiceProviders();
            resetSelection();
            selectServiceProvidersBtn.classList.add('border-green-500', 'bg-green-100');
        });

        // Send message
        sendNewMessage.addEventListener('click', function() {
            const message = document.getElementById('newMessageText').value.trim();

            if (!selectedRecipient) {
                alert('Please select a recipient');
                return;
            }

            if (!message) {
                alert('Please enter a message');
                return;
            }

            sendMessageToRecipient(selectedRecipient.id, selectedRecipientType, message);
        });

        function resetModal() {
            resetSelection();
            studentsSection.classList.add('hidden');
            serviceProvidersSection.classList.add('hidden');
            messageInputSection.classList.add('hidden');
            selectedRecipient = null;
            selectedRecipientType = null;
            document.getElementById('newMessageText').value = '';
        }

        function resetSelection() {
            selectStudentsBtn.classList.remove('border-blue-500', 'bg-blue-100');
            selectServiceProvidersBtn.classList.remove('border-green-500', 'bg-green-100');
        }

        function showSection(section) {
            studentsSection.classList.add('hidden');
            serviceProvidersSection.classList.add('hidden');
            section.classList.remove('hidden');
            messageInputSection.classList.remove('hidden');
        }

        function loadStudents() {
            const studentsList = document.getElementById('studentsList');
            const studentsLoading = document.getElementById('studentsLoading');
            const noStudents = document.getElementById('noStudents');

            studentsList.innerHTML = '';
            studentsLoading.classList.remove('hidden');
            noStudents.classList.add('hidden');

            fetch('{{ route("landlord.messages.get-students") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    studentsLoading.classList.add('hidden');

                    if (data.success && data.students && data.students.length > 0) {
                        data.students.forEach(student => {
                            const studentElement = document.createElement('div');
                            studentElement.className = 'p-3 hover:bg-gray-50 cursor-pointer student-item transition-colors';
                            studentElement.setAttribute('data-student-id', student.id);
                            studentElement.innerHTML = `
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold">${student.name.charAt(0)}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900">${student.name}</h5>
                                        <p class="text-sm text-gray-500">${student.email}</p>
                                        <p class="text-xs text-gray-400">${student.booked_hostels || 'No bookings'}</p>
                                    </div>
                                </div>
                            `;

                            studentElement.addEventListener('click', function() {
                                document.querySelectorAll('.student-item').forEach(item => {
                                    item.classList.remove('bg-blue-50', 'border-blue-200', 'border');
                                });
                                this.classList.add('bg-blue-50', 'border-blue-200', 'border');
                                selectedRecipient = student;
                            });

                            studentsList.appendChild(studentElement);
                        });
                    } else {
                        noStudents.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentsLoading.classList.add('hidden');
                    noStudents.classList.remove('hidden');
                });
        }

        function loadServiceProviders() {
            const serviceProvidersList = document.getElementById('serviceProvidersList');
            const serviceProvidersLoading = document.getElementById('serviceProvidersLoading');
            const noServiceProviders = document.getElementById('noServiceProviders');

            serviceProvidersList.innerHTML = '';
            serviceProvidersLoading.classList.remove('hidden');
            noServiceProviders.classList.add('hidden');

            fetch('{{ route("landlord.messages.get-service-providers") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    serviceProvidersLoading.classList.add('hidden');

                    if (data.success && data.service_providers && data.service_providers.length > 0) {
                        data.service_providers.forEach(provider => {
                            const providerElement = document.createElement('div');
                            providerElement.className = 'p-3 hover:bg-gray-50 cursor-pointer service-provider-item transition-colors';
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
                                    item.classList.remove('bg-green-50', 'border-green-200', 'border');
                                });
                                this.classList.add('bg-green-50', 'border-green-200', 'border');
                                selectedRecipient = provider;
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

        function sendMessageToRecipient(recipientId, recipientType, message) {
            sendNewMessage.disabled = true;
            sendNewMessage.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
                Sending...
            `;

            fetch('{{ route("landlord.messages.start-conversation") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    receiver_id: recipientId,
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                sendNewMessage.disabled = false;
                sendNewMessage.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Message
                `;

                if (data.success) {
                    newMessageModal.classList.add('hidden');
                    resetModal();
                    // Redirect to the new conversation
                    window.location.href = '{{ route("landlord.messages.index") }}?conversation_id=' + data.conversation_id;
                } else {
                    alert('Error sending message: ' + (data.message || 'Please try again'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                sendNewMessage.disabled = false;
                sendNewMessage.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Message
                `;
                alert('Error sending message. Please try again.');
            });
        }

        // Search functionality
        document.getElementById('searchStudents').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.student-item').forEach(item => {
                const studentName = item.querySelector('h5').textContent.toLowerCase();
                const studentEmail = item.querySelector('p.text-gray-500').textContent.toLowerCase();
                if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        document.getElementById('searchServiceProviders').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.service-provider-item').forEach(item => {
                const providerName = item.querySelector('h5').textContent.toLowerCase();
                const serviceType = item.querySelector('p.text-gray-500').textContent.toLowerCase();
                if (providerName.includes(searchTerm) || serviceType.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Conversation search
        document.getElementById('searchConversations').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.conversation-item').forEach(item => {
                const userName = item.getAttribute('data-user-name');
                const userType = item.getAttribute('data-user-type');
                if (userName.includes(searchTerm) || userType.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Conversation click handling
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function() {
                const conversationId = this.getAttribute('data-conversation-id');

                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('conversation_id', conversationId);
                window.history.pushState({}, '', url);

                // Reload to show the selected conversation
                window.location.reload();
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

    function sendMessage(form) {
    const formData = new FormData(form);
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();

    if (!message) {
        alert('Please enter a message');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Disable form and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

    // Get the conversation ID from the hidden input
    const conversationId = form.querySelector('input[name="conversation_id"]').value;
    
    // Build the URL correctly with the conversation parameter
// CORRECT - Use Laravel's route helper
const url = '{{ route("landlord.messages.send-message", ["conversation" => ":conversationId"]) }}'.replace(':conversationId', conversationId);

    fetch(url, {
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
            // Append the new message to the messages list without reloading
            appendNewMessage(data.message);
        } else {
            alert('Failed to send message: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Add this helper function to append new messages without reloading
function appendNewMessage(message) {
    const messagesList = document.getElementById('messagesList');
    const isSender = true; // Current user is the sender
    const color = '{{ $color ?? "blue" }}';
    
    const messageElement = document.createElement('div');
    messageElement.className = 'flex justify-end message-item fade-in';
    messageElement.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg message-bubble bg-${color}-600 text-white">
            <p class="text-sm">${escapeHtml(message.message)}</p>
            <p class="text-xs mt-1 text-${color}-200 text-right">
                ${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
            </p>
        </div>
    `;
    
    messagesList.appendChild(messageElement);
    
    // Scroll to bottom
    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
    });
    </script>
</body>
</html>
