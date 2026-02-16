<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Dashboard - Hostel Management System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .dropdown-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <div class="flex items-center space-x-2">
                            <svg class="h-9 w-9 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-xl font-bold text-gray-900">HostelHub</span>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('favorites.index') }}"
                           class="nav-link text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                My Favorites
                            </div>
                        </a>

                        <a href="{{ route('student.messages') }}"
                           class="nav-link text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors duration-200 relative">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                Messages
                            </div>
                            @php
                                $unreadMessagesCount = \App\Models\Message::where('receiver_id', Auth::id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            @if($unreadMessagesCount > 0)
                                <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications Dropdown -->
                    @auth
                    @php
                        $unreadCount = \App\Services\NotificationService::getUnreadCount(Auth::id());
                        $notifications = \App\Services\NotificationService::getRecentNotifications(Auth::id(), 5);
                    @endphp

                    <div class="relative" x-data="{ open: false }">
                        <!-- Notifications Button -->
                        <button @click="open = !open"
                                class="relative p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>

                            <!-- Unread Count Badge -->
                            @if($unreadCount > 0)
                                <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
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
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg dropdown-shadow ring-1 ring-black ring-opacity-5 z-50 max-h-96 overflow-y-auto">

                            <!-- Header -->
                            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-lg">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                    @if($notifications->count() > 0)
                                        <button onclick="markAllAsRead()"
                                                class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
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
                                                            class="text-xs text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                                        Mark read
                                                    </button>
                                                @endif
                                                <button onclick="deleteNotification({{ $notification->id }})"
                                                        class="text-xs text-red-600 hover:text-red-800 transition-colors duration-200">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <!-- Empty State -->
                                    <div class="p-8 text-center">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No notifications yet</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer -->
                            @if($notifications->count() > 0)
                                <div class="p-3 border-t border-gray-100 bg-gray-50 rounded-b-lg">
                                    <a href="{{ route('student.notifications') }}"
                                       class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                                        View all notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endauth

                    <!-- User Greeting -->
                    <div class="hidden md:flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Hi, {{ Auth::user()->name }}</span>
                    </div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-600 hover:text-white hover:bg-blue-600 px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-600 transition-all duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600">Manage your accommodation and stay updated with your bookings.</p>

                            @if(Auth::user()->studentProfile)
                                <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-green-800 font-medium">Profile Complete</span>
                                    </div>
                                    <p class="text-sm text-green-700 mt-1">Your student profile is ready for hostel bookings.</p>
                                </div>
                            @else
                                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-yellow-800 font-medium">Profile Incomplete</span>
                                    </div>
                                    <p class="text-sm text-yellow-700 mt-1">Complete your profile to access all features.</p>
                                    <a href="{{ route('student.profile') }}" class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Complete Profile Now →
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->studentProfile)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-800">Student Information</h3>
                    <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                        <div>
                            <span class="font-medium">Admission No:</span>
                            <span>{{ Auth::user()->studentProfile->admission_number }}</span>
                        </div>
                        <div>
                            <span class="font-medium">ID Number:</span>
                            <span>{{ Auth::user()->studentProfile->id_number }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Institution:</span>
                            <span>{{ Auth::user()->studentProfile->institution_name }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Course:</span>
                            <span>{{ Auth::user()->studentProfile->course }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Year of Study:</span>
                            <span>{{ Auth::user()->studentProfile->year_of_study }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Gender:</span>
                            <span class="capitalize">{{ Auth::user()->studentProfile->gender }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-yellow-700">Please complete your student profile to access all features.</p>
                    <a href="{{ route('student.profile') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Complete Profile →
                    </a>
                </div>
            @endif

            <!-- Quick Stats -->
            @php
                $userBookings = Auth::user()->bookings ?? collect();
                $activeBookings = $userBookings->where('booking_status', 'confirmed')->count();
                $pendingPayments = $userBookings->where('payment_status', 'pending')->where('booking_status', 'confirmed')->count();
                $totalBookings = $userBookings->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Bookings</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $activeBookings }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Payments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pendingPayments }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalBookings }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Notifications</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $unreadCount ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Recent Bookings</h3>
                        <a href="{{ route('student.my-bookings') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center text-sm transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            View All Bookings
                        </a>
                    </div>

                    @if($userBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($userBookings->take(3) as $booking)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $booking->hostel->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $booking->hostel->location }}</p>
                                            <div class="flex items-center space-x-4 mt-2 text-sm">
                                                <span class="text-gray-500">Check-in: {{ $booking->check_in_date->format('M d, Y') }}</span>
                                                <span class="text-gray-500">Duration: {{ $booking->duration_months }} months</span>
                                                <span class="text-gray-500">Amount: KSh {{ number_format($booking->total_amount) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                                   ($booking->booking_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($booking->booking_status) }}
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                                   ($booking->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($booking->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-xs text-gray-500">
                                            Booked {{ $booking->created_at->diffForHumans() }}
                                        </span>
                                        <div class="flex space-x-2">
                                            @if($booking->booking_status === 'confirmed' && $booking->payment_status === 'pending')
                                                <a href="{{ route('student.payment.form', $booking->id) }}"
                                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                                    Make Payment
                                                </a>
                                            @endif
                                            <a href="{{ route('student.booking.details', $booking->id) }}"
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                                View Details
                                            </a>
                                            @if($booking->booking_status === 'pending')
                                                <button onclick="cancelBooking({{ $booking->id }})"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                                    Cancel
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h4>
                            <p class="text-gray-500 mb-4">Start by searching for hostels and making your first booking.</p>
                            <a href="{{ route('student.search-hostels') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search Hostels
                            </a>
                        </div>
                    @endif
                </div>
            </div>

<!-- In the Quick Actions section -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <a href="{{ route('student.search-hostels') }}"
       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
        <svg class="w-8 h-8 text-blue-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <div>
            <h4 class="font-semibold">Search Hostels</h4>
            <p class="text-sm text-gray-600">Find your perfect accommodation</p>
        </div>
    </a>

    <a href="{{ route('student.my-bookings') }}"
       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
        <svg class="w-8 h-8 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <div>
            <h4 class="font-semibold">My Bookings</h4>
            <p class="text-sm text-gray-600">View and manage all bookings</p>
        </div>
    </a>

    <!-- Add Messages to Quick Actions -->
    <a href="{{ route('student.messages') }}"
       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200 relative">
        <svg class="w-8 h-8 text-purple-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <div>
            <h4 class="font-semibold">Messages</h4>
            <p class="text-sm text-gray-600">Chat with landlords</p>
        </div>
        @php
            $unreadMessagesCount = \App\Models\Message::where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();
        @endphp
        @if($unreadMessagesCount > 0)
            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge">
                {{ $unreadMessagesCount }}
            </span>
        @endif
    </a>

    <a href="{{ route('student.profile') }}"
       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
        <svg class="w-8 h-8 text-orange-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <div>
            <h4 class="font-semibold">Update Profile</h4>
            <p class="text-sm text-gray-600">Manage your personal information</p>
        </div>
    </a>
</div>
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- JavaScript for Actions -->
    <script>
        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch(`/student/bookings/${bookingId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to cancel booking');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cancelling the booking');
                });
            }
        }

        // Existing notification functions...
        function markAsRead(notificationId) {
            // ... existing code ...
        }

        function markAllAsRead() {
            // ... existing code ...
        }

        function deleteNotification(notificationId) {
            // ... existing code ...
        }

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(message => {
                message.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
