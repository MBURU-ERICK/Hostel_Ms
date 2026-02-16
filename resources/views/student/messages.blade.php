<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Messages - Hostel Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('student.my-bookings') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        My Bookings
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Messages Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <p class="text-gray-600">Your conversations with landlords</p>
            </div>

            <div class="p-6">
                @if($bookingsWithMessages->count() > 0)
                    <div class="space-y-4">
                        @foreach($bookingsWithMessages as $booking)
                            <a href="{{ route('student.booking.messages', $booking->id) }}"
                               class="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $booking->hostel->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $booking->hostel->location }}</p>
                                        @if($booking->messages->count() > 0)
                                            <p class="text-sm text-gray-500 mt-2">
                                                Last message: {{ $booking->messages->first()->created_at->diffForHumans() }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                               ($booking->booking_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($booking->booking_status) }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $booking->messages->count() }} messages
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                        <p class="text-gray-500 mb-4">You'll see your conversations with landlords here once you start messaging.</p>
                        <a href="{{ route('student.my-bookings') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            View My Bookings
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
