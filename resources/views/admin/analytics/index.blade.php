<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Analytics - HostelHub Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
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
                        <h1 class="text-3xl font-bold text-gray-900">System Analytics</h1>
                        <p class="text-gray-600 mt-2">Comprehensive insights, payment analytics, and performance metrics</p>
                    </div>
                    <div class="flex space-x-3">
                        <select id="timeRange" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="7">Last 7 Days</option>
                            <option value="30" selected>Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                            <option value="365">Last Year</option>
                            <option value="all">All Time</option>
                        </select>
                        <div class="relative">
                            <button onclick="toggleReportMenu()" class="bg-indigo-600 text-white border border-indigo-600 px-6 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center">
                                <i class="fas fa-download mr-2"></i>
                                Export Report
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </button>
                            <div id="reportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                <div class="p-2">
                                    <button onclick="generatePDFReport()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                        <i class="fas fa-file-pdf mr-2 text-red-500"></i> PDF Report
                                    </button>
                                    <button onclick="exportToExcel()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                        <i class="fas fa-file-excel mr-2 text-green-600"></i> Excel Report
                                    </button>
                                    <button onclick="printReport()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                        <i class="fas fa-print mr-2 text-blue-600"></i> Print Report
                                    </button>
                                    <div class="border-t border-gray-200 my-2"></div>
                                    <button onclick="exportPaymentReport()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                        <i class="fas fa-credit-card mr-2 text-purple-600"></i> Payment Report
                                    </button>
                                    <button onclick="exportUserReport()" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                        <i class="fas fa-users mr-2 text-indigo-600"></i> User Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Content Wrapper for PDF -->
            <div id="analytics-content">
                <!-- User Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Students Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Students</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
                                <p class="text-sm {{ $studentGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    <i class="fas fa-arrow-{{ $studentGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($studentGrowth) }}% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-user-graduate text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Landlords Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Landlords</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalLandlords) }}</p>
                                <p class="text-sm {{ $landlordGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    <i class="fas fa-arrow-{{ $landlordGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($landlordGrowth) }}% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-building text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Service Providers Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Service Providers</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalServiceProviders) }}</p>
                                <p class="text-sm {{ $serviceProviderGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    <i class="fas fa-arrow-{{ $serviceProviderGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($serviceProviderGrowth) }}% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-tools text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Users Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Users</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalActiveUsers) }}</p>
                                <p class="text-sm text-green-600 mt-1">
                                    <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                                    {{ $activePercentage }}% of total
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Revenue Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-900">KSh {{ number_format($totalRevenue, 2) }}</p>
                                <p class="text-sm {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} mr-1"></i>
                                    {{ abs($revenueGrowth) }}% from last month
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Successful Payments Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Successful Payments</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSuccessfulPayments) }}</p>
                                <p class="text-sm text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ $successRate }}% success rate
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payments Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPendingPayments) }}</p>
                                <p class="text-sm text-yellow-600 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    KSh {{ number_format($pendingAmount, 2) }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Payments Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Failed Payments</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalFailedPayments) }}</p>
                                <p class="text-sm text-red-600 mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    KSh {{ number_format($failedAmount, 2) }}
                                </p>
                            </div>
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Payment Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Average Payment Value -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Average Payment Value</h3>
                        <p class="text-3xl font-bold text-gray-900">KSh {{ number_format($averagePaymentValue, 2) }}</p>
                        <div class="mt-2 flex items-center text-sm">
                            <span class="text-gray-500">Min: KSh {{ number_format($minPaymentValue, 2) }}</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-gray-500">Max: KSh {{ number_format($maxPaymentValue, 2) }}</span>
                        </div>
                    </div>

                    <!-- Today's Revenue -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Today's Revenue</h3>
                        <p class="text-3xl font-bold text-gray-900">KSh {{ number_format($todayRevenue, 2) }}</p>
                        <div class="mt-2 flex items-center text-sm">
                            <span class="text-gray-500">{{ $todayTransactions }} transactions</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-green-600">{{ $todaySuccessful }} successful</span>
                        </div>
                    </div>

                    <!-- This Month's Revenue -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">This Month's Revenue</h3>
                        <p class="text-3xl font-bold text-gray-900">KSh {{ number_format($monthRevenue, 2) }}</p>
                        <div class="mt-2 flex items-center text-sm">
                            <span class="text-gray-500">{{ $monthTransactions }} transactions</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-green-600">{{ $monthSuccessful }} successful</span>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Payment Trends Chart -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Trends</h3>
                        <div class="h-80">
                            <canvas id="paymentTrendsChart"></canvas>
                        </div>
                    </div>

                    <!-- Payment Status Distribution -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Status Distribution</h3>
                        <div class="h-80">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Revenue by Source Chart -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Source</h3>
                        <div class="h-80">
                            <canvas id="revenueBySourceChart"></canvas>
                        </div>
                    </div>

                    <!-- User Growth Chart -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth by Type</h3>
                        <div class="h-80">
                            <canvas id="userGrowthByTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Detailed Tables Section -->
                <div class="grid grid-cols-1 gap-8">
                    <!-- Payment Summary Table -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                            <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View All <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                            KSh {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($payment->status == 'successful') bg-green-100 text-green-800
                                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ucfirst($payment->payment_method) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Monthly Payment Summary Table -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Payment Summary</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payments</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Successful</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failed</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($monthlyPaymentSummary as $summary)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $summary->month }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($summary->total_payments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($summary->successful_payments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($summary->failed_payments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            KSh {{ number_format($summary->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $summary->success_rate >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $summary->success_rate }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format($totalPayments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">{{ number_format($totalSuccessfulPayments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">{{ number_format($totalFailedPayments) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">KSh {{ number_format($totalRevenue, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $successRate }}%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Top Paying Users Table -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Paying Users</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payments</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($topPayingUsers as $index => $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($user->user_type == 'student') bg-blue-100 text-blue-800
                                                @elseif($user->user_type == 'landlord') bg-purple-100 text-purple-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($user->payments_count) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                            KSh {{ number_format($user->total_spent, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->last_payment_date ? \Carbon\Carbon::parse($user->last_payment_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top Hostels by Revenue -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Hostels by Revenue</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bookings</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg per Booking</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($topHostels as $index => $hostel)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $hostel->name }}</div>
                                            <div class="text-xs text-gray-500">By {{ $hostel->landlord->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $hostel->location }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($hostel->bookings_count) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                            KSh {{ number_format($hostel->total_revenue, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            KSh {{ number_format($hostel->average_booking_value, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Report menu toggle
        function toggleReportMenu() {
            const menu = document.getElementById('reportMenu');
            menu.classList.toggle('hidden');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('reportMenu');
            const button = event.target.closest('button');
            if (!button || !button.innerHTML.includes('Export Report')) {
                menu.classList.add('hidden');
            }
        });

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Payment Trends Chart
            const paymentTrendsCtx = document.getElementById('paymentTrendsChart').getContext('2d');
            new Chart(paymentTrendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($paymentTrends->pluck('date')) !!},
                    datasets: [
                        {
                            label: 'Successful Payments',
                            data: {!! json_encode($paymentTrends->pluck('successful')) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Failed Payments',
                            data: {!! json_encode($paymentTrends->pluck('failed')) !!},
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        }
                    }
                }
            });

            // Payment Status Chart
            const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
            new Chart(paymentStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Successful', 'Pending', 'Failed', 'Cancelled'],
                    datasets: [{
                        data: [
                            {{ $totalSuccessfulPayments }},
                            {{ $totalPendingPayments }},
                            {{ $totalFailedPayments }},
                            {{ $totalCancelledPayments }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6b7280'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Revenue by Source Chart
            const revenueBySourceCtx = document.getElementById('revenueBySourceChart').getContext('2d');
            new Chart(revenueBySourceCtx, {
                type: 'pie',
                data: {
                    labels: ['Booking Revenue', 'Service Revenue'],
                    datasets: [{
                        data: [
                            {{ $totalBookingRevenue }},
                            {{ $totalServiceRevenue }}
                        ],
                        backgroundColor: ['#3b82f6', '#10b981'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: KSh ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // User Growth by Type Chart
            const userGrowthByTypeCtx = document.getElementById('userGrowthByTypeChart').getContext('2d');
            new Chart(userGrowthByTypeCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($userGrowthByType->pluck('date')) !!},
                    datasets: [
                        {
                            label: 'Students',
                            data: {!! json_encode($userGrowthByType->pluck('students')) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Landlords',
                            data: {!! json_encode($userGrowthByType->pluck('landlords')) !!},
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        }
                    }
                }
            });

            // Time range filter
            document.getElementById('timeRange').addEventListener('change', function(e) {
                window.location.href = '{{ route("admin.analytics") }}?days=' + e.target.value;
            });
        });

        // PDF Report Generation
        async function generatePDFReport() {
            const { jsPDF } = window.jspdf;
            
            const exportBtn = event.currentTarget;
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
            exportBtn.disabled = true;

            try {
                const pdf = new jsPDF('p', 'mm', 'a4');
                
                // Title page
                pdf.setFontSize(24);
                pdf.setTextColor(79, 70, 229);
                pdf.text('HostelHub Complete Analytics Report', 20, 20);
                
                pdf.setFontSize(12);
                pdf.setTextColor(75, 85, 99);
                pdf.text('Generated: ' + new Date().toLocaleString(), 20, 30);
                
                const timeRange = document.getElementById('timeRange');
                const selectedRange = timeRange.options[timeRange.selectedIndex].text;
                pdf.text('Time Period: ' + selectedRange, 20, 37);
                
                // Executive Summary
                pdf.setFontSize(16);
                pdf.setTextColor(17, 24, 39);
                pdf.text('Executive Summary', 20, 50);
                
                pdf.setFontSize(11);
                pdf.setTextColor(55, 65, 81);
                
                const summary = [
                    `Total Students: {{ number_format($totalStudents) }} ({{ $studentGrowth }}% growth)`,
                    `Total Landlords: {{ number_format($totalLandlords) }} ({{ $landlordGrowth }}% growth)`,
                    `Total Service Providers: {{ number_format($totalServiceProviders) }} ({{ $serviceProviderGrowth }}% growth)`,
                    `Total Revenue: KSh {{ number_format($totalRevenue, 2) }} ({{ $revenueGrowth }}% growth)`,
                    `Total Payments: {{ number_format($totalPayments) }} ({{ $successRate }}% success rate)`,
                    `Average Payment: KSh {{ number_format($averagePaymentValue, 2) }}`,
                    `Today's Revenue: KSh {{ number_format($todayRevenue, 2) }}`,
                    `This Month: KSh {{ number_format($monthRevenue, 2) }}`
                ];
                
                let yPosition = 60;
                summary.forEach(line => {
                    pdf.text(line, 20, yPosition);
                    yPosition += 7;
                });

                // Payment Summary Table
                yPosition += 10;
                pdf.setFontSize(14);
                pdf.setTextColor(79, 70, 229);
                pdf.text('Payment Summary', 20, yPosition);
                yPosition += 10;
                
                const paymentHeaders = [['Status', 'Count', 'Amount', 'Percentage']];
                const paymentData = [
                    ['Successful', '{{ number_format($totalSuccessfulPayments) }}', 'KSh {{ number_format($totalRevenue, 2) }}', '{{ $successRate }}%'],
                    ['Pending', '{{ number_format($totalPendingPayments) }}', 'KSh {{ number_format($pendingAmount, 2) }}', '{{ $pendingPercentage }}%'],
                    ['Failed', '{{ number_format($totalFailedPayments) }}', 'KSh {{ number_format($failedAmount, 2) }}', '{{ $failedPercentage }}%'],
                    ['Cancelled', '{{ number_format($totalCancelledPayments) }}', 'KSh {{ number_format($cancelledAmount, 2) }}', '{{ $cancelledPercentage }}%']
                ];
                
                pdf.autoTable({
                    startY: yPosition,
                    head: paymentHeaders,
                    body: paymentData,
                    theme: 'striped',
                    headStyles: { fillColor: [79, 70, 229] },
                    styles: { fontSize: 9 }
                });
                
                yPosition = pdf.lastAutoTable.finalY + 15;
                
                // Monthly Payment Summary
                pdf.setFontSize(14);
                pdf.setTextColor(79, 70, 229);
                pdf.text('Monthly Payment Summary', 20, yPosition);
                yPosition += 10;
                
                const monthlyHeaders = [['Month', 'Total', 'Successful', 'Failed', 'Amount', 'Rate']];
                const monthlyData = {!! json_encode($monthlyPaymentSummary->map(function($item) {
                    return [
                        $item->month,
                        (string)$item->total_payments,
                        (string)$item->successful_payments,
                        (string)$item->failed_payments,
                        'KSh ' . number_format($item->total_amount, 2),
                        $item->success_rate . '%'
                    ];
                })) !!};
                
                pdf.autoTable({
                    startY: yPosition,
                    head: monthlyHeaders,
                    body: monthlyData,
                    theme: 'striped',
                    headStyles: { fillColor: [79, 70, 229] },
                    styles: { fontSize: 9 }
                });
                
                yPosition = pdf.lastAutoTable.finalY + 15;
                
                // Top Paying Users
                if (yPosition > 250) {
                    pdf.addPage();
                    yPosition = 20;
                }
                
                pdf.setFontSize(14);
                pdf.setTextColor(79, 70, 229);
                pdf.text('Top Paying Users', 20, yPosition);
                yPosition += 10;
                
                const userHeaders = [['Rank', 'User', 'Type', 'Payments', 'Total Spent']];
                const userData = {!! json_encode($topPayingUsers->map(function($user, $index) {
                    return [
                        '#' . ($index + 1),
                        $user->name,
                        ucfirst(str_replace('_', ' ', $user->user_type)),
                        (string)$user->payments_count,
                        'KSh ' . number_format($user->total_spent, 2)
                    ];
                })) !!};
                
                pdf.autoTable({
                    startY: yPosition,
                    head: userHeaders,
                    body: userData,
                    theme: 'striped',
                    headStyles: { fillColor: [79, 70, 229] },
                    styles: { fontSize: 9 }
                });
                
                // Save the PDF
                const fileName = `HostelHub_Complete_Report_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(fileName);
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF report. Please try again.');
            } finally {
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                document.getElementById('reportMenu').classList.add('hidden');
            }
        }

        // Export to Excel
        function exportToExcel() {
            // Create CSV content
            let csv = "Report Type,Count,Amount\n";
            csv += `Total Students,{{ $totalStudents }},\n`;
            csv += `Total Landlords,{{ $totalLandlords }},\n`;
            csv += `Total Service Providers,{{ $totalServiceProviders }},\n`;
            csv += `Total Payments,{{ $totalPayments }},KSh {{ number_format($totalRevenue, 2) }}\n`;
            csv += `Successful Payments,{{ $totalSuccessfulPayments }},KSh {{ number_format($totalRevenue, 2) }}\n`;
            csv += `Pending Payments,{{ $totalPendingPayments }},KSh {{ number_format($pendingAmount, 2) }}\n`;
            csv += `Failed Payments,{{ $totalFailedPayments }},KSh {{ number_format($failedAmount, 2) }}\n`;
            
            // Add monthly summary
            csv += "\nMonth,Total Payments,Successful,Failed,Amount,Success Rate\n";
            @foreach($monthlyPaymentSummary as $summary)
            csv += "{{ $summary->month }},{{ $summary->total_payments }},{{ $summary->successful_payments }},{{ $summary->failed_payments }},KSh {{ number_format($summary->total_amount, 2) }},{{ $summary->success_rate }}%\n";
            @endforeach
            
            // Create download
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `HostelHub_Report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            document.getElementById('reportMenu').classList.add('hidden');
        }

        // Print Report
        function printReport() {
            window.print();
            document.getElementById('reportMenu').classList.add('hidden');
        }

        // Export Payment Report
        function exportPaymentReport() {
            window.location.href = '{{ route("admin.payments.index") }}?export=csv&days=' + document.getElementById('timeRange').value;
            document.getElementById('reportMenu').classList.add('hidden');
        }

        // Export User Report
        function exportUserReport() {
            window.location.href = '{{ route("admin.users") }}?export=csv&days=' + document.getElementById('timeRange').value;
            document.getElementById('reportMenu').classList.add('hidden');
        }
    </script>

    <!-- Add autoTable plugin for PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>
</html>