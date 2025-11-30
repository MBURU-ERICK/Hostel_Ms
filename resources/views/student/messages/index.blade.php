<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - {{ $booking->hostel->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .message-container {
            max-height: 60vh;
            overflow-y: auto;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.visible {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
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
                        <a href="{{ route('student.my-bookings') }}"
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            My Bookings
                        </a>
                        <a href="{{ route('student.dashboard') }}"
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Messages Section -->
        <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
             <!-- Header -->
<div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $booking->hostel->name }}
                </h1>
                <p class="text-gray-600">
                    Conversation with {{ $booking->hostel->landlord->name ?? 'Landlord' }} • Booking Reference: #{{ $booking->id }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Status:
                <span class="font-medium capitalize
                    {{ $booking->booking_status === 'confirmed' ? 'text-green-600' :
                       ($booking->booking_status === 'pending' ? 'text-yellow-600' :
                       ($booking->booking_status === 'cancelled' ? 'text-red-600' : 'text-gray-600')) }}">
                    {{ $booking->booking_status }}
                </span>
            </p>
            <p class="text-sm text-gray-500 mt-1">
                {{ $booking->hostel->location }}
            </p>
            @if($booking->hostel->landlord)
                <p class="text-sm text-gray-500 mt-1">
                    Landlord: {{ $booking->hostel->landlord->name }}
                </p>
            @endif
        </div>
    </div>
</div>

                <!-- Messages Container -->
                <div x-data="messageSystem({{ $booking->id }})" class="p-6">
                    <!-- Messages List -->
                    <div class="message-container mb-4 space-y-4 p-4 bg-gray-50 rounded-lg"
                         x-ref="messagesContainer"
                         x-init="scrollToBottom()">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex" :class="message.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                                <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg shadow-sm"
                                     :class="message.sender_id === {{ Auth::id() }}
                                         ? 'bg-blue-500 text-white rounded-br-none'
                                         : 'bg-white text-gray-800 border border-gray-200 rounded-bl-none'">
                                    <p x-text="message.message" class="text-sm break-words"></p>
                                    <p class="text-xs mt-2 opacity-75 flex justify-between items-center"
                                       :class="message.sender_id === {{ Auth::id() }} ? 'text-blue-100' : 'text-gray-500'">
                                        <span x-text="new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                        <span x-text="message.sender_id === {{ Auth::id() }} ? 'You' : message.sender.name" class="ml-2 font-medium"></span>
                                    </p>
                                </div>
                            </div>
                        </template>

                        <!-- Typing Indicator -->
                        <div x-show="isTyping" class="flex justify-start">
                            <div class="bg-white border border-gray-200 rounded-lg rounded-bl-none px-4 py-3">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="border-t border-gray-200 pt-4">
                        <form @submit.prevent="sendMessage" class="flex space-x-3">
                            <div class="flex-1">
                                <input type="text"
                                       x-model="newMessage"
                                       @input="handleTyping"
                                       placeholder="Type your message..."
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       :disabled="sending || {{ $booking->status === 'cancelled' ? 'true' : 'false' }}">
                                <p x-show="{{ $booking->status === 'cancelled' }}" class="text-red-500 text-xs mt-1">
                                    Cannot send messages for cancelled bookings
                                </p>
                            </div>
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                    :disabled="!newMessage.trim() || sending || {{ $booking->status === 'cancelled' ? 'true' : 'false' }}">
                                <span x-show="!sending" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send
                                </span>
                                <span x-show="sending" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function messageSystem(bookingId) {
            return {
                messages: [],
                newMessage: '',
                sending: false,
                polling: null,
                isTyping: false,
                typingTimeout: null,

                async init() {
                    await this.loadMessages();
                    this.startPolling();
                    this.scrollToBottom();
                },

                async loadMessages() {
                    try {
                        const response = await fetch(`/bookings/${bookingId}/messages`);
                        if (response.ok) {
                            this.messages = await response.json();
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (error) {
                        console.error('Error loading messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.sending) return;

                    this.sending = true;

                    try {
                        const response = await fetch(`/bookings/${bookingId}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                message: this.newMessage
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.messages.push(data.message);
                            this.newMessage = '';
                            this.$nextTick(() => this.scrollToBottom());
                        } else {
                            alert('Failed to send message. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please check your connection and try again.');
                    } finally {
                        this.sending = false;
                    }
                },

                handleTyping() {
                    // You can implement typing indicators here
                    // This would require WebSockets for real-time functionality
                },

                startPolling() {
                    this.polling = setInterval(async () => {
                        await this.loadMessages();
                    }, 3000); // Poll every 3 seconds for new messages
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                destroy() {
                    if (this.polling) {
                        clearInterval(this.polling);
                    }
                    if (this.typingTimeout) {
                        clearTimeout(this.typingTimeout);
                    }
                }
            }
        }
    </script>
</body>
</html>
