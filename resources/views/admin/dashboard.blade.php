<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                       class="flex items-center px-4 py-3 text-gray-700 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-building w-5 h-5 mr-3"></i>
                        Hostels
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                        Bookings
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-credit-card w-5 h-5 mr-3"></i>
                        Payments
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
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-comments w-5 h-5 mr-3"></i>
                        Messages
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
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
                        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                        <p class="text-gray-600 mt-2">Overview of the HostelHub system and payment analytics</p>
                    </div>
                    <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg shadow-sm">
                        <i class="far fa-calendar-alt mr-2"></i>
                        {{ now()->format('l, F j, Y') }}
                    </div>
                </div>
            </div>

            <!-- System Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="text-blue-600">{{ number_format($stats['total_students']) }}</span> Students · 
                                <span class="text-green-600">{{ number_format($stats['total_landlords']) }}</span> Landlords
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Hostels -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Hostels</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_hostels']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="text-green-600">{{ $stats['total_hostels'] }}</span> Available
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-building text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bookings'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="text-green-600">{{ $stats['confirmed_bookings'] ?? 0 }}</span> Confirmed · 
                                <span class="text-yellow-600">{{ $stats['pending_bookings'] ?? 0 }}</span> Pending
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-calendar-check text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Service Requests -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Service Requests</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_service_requests']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="text-yellow-600">{{ $stats['pending_service_requests'] }}</span> Pending
                            </p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <i class="fas fa-tools text-2xl text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-100">Total Revenue</p>
                            <p class="text-2xl font-bold">KSh {{ number_format($paymentStats['total_revenue'] ?? 0, 2) }}</p>
                            <p class="text-xs text-green-100 mt-1">
                                {{ $paymentStats['successful_payments'] ?? 0 }} successful payments
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-dollar-sign text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Today's Revenue -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-100">Today's Revenue</p>
                            <p class="text-2xl font-bold">KSh {{ number_format($paymentStats['today_revenue'] ?? 0, 2) }}</p>
                            <p class="text-xs text-blue-100 mt-1">
                                {{ $paymentStats['today_transactions'] ?? 0 }} transactions
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-calendar-day text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- This Month's Revenue -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-100">This Month</p>
                            <p class="text-2xl font-bold">KSh {{ number_format($paymentStats['this_month_revenue'] ?? 0, 2) }}</p>
                            <p class="text-xs text-purple-100 mt-1">
                                ↑ {{ $paymentStats['monthly_growth'] ?? 0 }}% from last month
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-calendar-alt text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Average Payment -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-100">Average Payment</p>
                            <p class="text-2xl font-bold">KSh {{ number_format($paymentStats['average_payment'] ?? 0, 2) }}</p>
                            <p class="text-xs text-yellow-100 mt-1">
                                Min: KSh {{ number_format($paymentStats['min_payment'] ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Successful</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($paymentStats['successful_payments'] ?? 0) }}</p>
                        </div>
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($paymentStats['pending_payments'] ?? 0) }}</p>
                        </div>
                        <div class="text-yellow-500">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Failed</p>
                            <p class="text-xl font-bold text-gray-900">{{ number_format($paymentStats['failed_payments'] ?? 0) }}</p>
                        </div>
                        <div class="text-red-500">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-gray-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Success Rate</p>
                            <p class="text-xl font-bold text-gray-900">{{ $paymentStats['success_rate'] ?? 0 }}%</p>
                        </div>
                        <div class="text-gray-500">
                            <i class="fas fa-percent text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Distribution and Payment Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- User Distribution -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Students</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $stats['total_students'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($stats['total_students'] / $stats['total_users'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Landlords</span>
                                <span class="text-sm font-semibold text-green-600">{{ $stats['total_landlords'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($stats['total_landlords'] / $stats['total_users'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Service Providers</span>
                                <span class="text-sm font-semibold text-purple-600">{{ $stats['total_service_providers'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($stats['total_service_providers'] / $stats['total_users'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Distribution -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Status Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Successful</span>
                                <span class="text-sm font-semibold text-green-600">{{ $paymentStats['successful_payments'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($paymentStats['success_rate'] ?? 0) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Pending</span>
                                <span class="text-sm font-semibold text-yellow-600">{{ $paymentStats['pending_payments'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $paymentStats['pending_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Failed</span>
                                <span class="text-sm font-semibold text-red-600">{{ $paymentStats['failed_payments'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $paymentStats['failed_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recent Payments -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                        <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @forelse($recentPayments ?? [] as $payment)
                            <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $payment->user->name ?? 'N/A' }}</h4>
                                        <p class="text-xs text-gray-500">{{ $payment->user->email ?? 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        @if($payment->status == 'successful') bg-green-100 text-green-800
                                        @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-green-600">KSh {{ number_format($payment->amount, 2) }}</span>
                                    <span class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</span>
                                </div>
                                @if($payment->transaction_id)
                                    <p class="text-xs text-gray-400 mt-1">TXN: {{ $payment->transaction_id }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent payments</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                        <a href="{{ route('admin.bookings') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @forelse($recentBookings as $booking)
                            <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $booking->hostel->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $booking->user->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            {{ $booking->booking_status == 'confirmed' ? 'bg-green-100 text-green-800' : 
                                               ($booking->booking_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($booking->booking_status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($booking->booking_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-sm font-semibold text-green-600">KSh {{ number_format($booking->total_amount, 2) }}</span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            <i class="far fa-calendar mr-1"></i>{{ $booking->check_in_date->format('M d') }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($booking->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        Payment: {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent bookings</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Users and Pending Approvals -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                        <a href="{{ route('admin.users') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @forelse($recentUsers as $user)
                            <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-indigo-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $user->user_type == 'student' ? 'bg-blue-100 text-blue-800' : 
                                           ($user->user_type == 'landlord' ? 'bg-green-100 text-green-800' : 
                                           ($user->user_type == 'service_provider' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span><i class="fas fa-phone mr-1"></i> {{ $user->phone ?? 'No phone' }}</span>
                                    <span>{{ $user->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent users</p>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Pending Approvals</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_approvals'] ?? 0 }} Pending
                        </span>
                    </div>
                    <div class="p-6">
                        @forelse($pendingApprovals ?? [] as $user)
                            <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                        {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs text-gray-500"><i class="far fa-clock mr-1"></i> {{ $user->created_at->diffForHumans() }}</span>
                                    <div class="space-x-2">
                                        <a href="{{ route('admin.users.approve', $user->id) }}" 
                                           class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700">
                                            Approve
                                        </a>
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="text-xs bg-gray-600 text-white px-3 py-1 rounded-lg hover:bg-gray-700">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No pending approvals</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <a href="{{ route('admin.users') }}"
                       class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="p-3 bg-blue-100 rounded-full mb-2">
                            <i class="fas fa-users text-xl text-blue-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Manage Users</span>
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="p-3 bg-green-100 rounded-full mb-2">
                            <i class="fas fa-building text-xl text-green-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Manage Hostels</span>
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                       class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="p-3 bg-yellow-100 rounded-full mb-2">
                            <i class="fas fa-credit-card text-xl text-yellow-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">View Payments</span>
                    </a>
                    <a href="{{ route('admin.service-providers') }}"
                       class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="p-3 bg-purple-100 rounded-full mb-2">
                            <i class="fas fa-user-cog text-xl text-purple-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Service Providers</span>
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="p-3 bg-red-100 rounded-full mb-2">
                            <i class="fas fa-chart-line text-xl text-red-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Analytics</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin dashboard loaded with payment stats');
        });
    </script>
</body>
</html>