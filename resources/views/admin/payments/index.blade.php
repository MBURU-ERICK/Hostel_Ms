<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Payment Management - HostelHub Admin</title>

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
                       class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-lg">
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
                        <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
                        <p class="text-gray-600 mt-2">View and manage all payment transactions</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="exportPayments()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Export CSV
                        </button>
                        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-100">Total Revenue</p>
                            <p class="text-2xl font-bold">KSh {{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-dollar-sign text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Successful Payments -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-100">Successful</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['successful_payments'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-100">Pending</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['pending_payments'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Failed Payments -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-100">Failed</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['failed_payments'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                            <i class="fas fa-times-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's and Monthly Revenue -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-calendar-day text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Today's Revenue</h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">KSh {{ number_format($stats['today_revenue'] ?? 0, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $stats['today_transactions'] ?? 0 }} transactions today</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">This Month's Revenue</h3>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">KSh {{ number_format($stats['this_month_revenue'] ?? 0, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ $stats['this_month_count'] ?? 0 }} transactions this month</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-filter mr-2 text-indigo-600"></i>
                        Filter Payments
                    </h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">All Statuses</option>
                                <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" placeholder="Transaction ID, Student..." value="{{ request('search') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="md:col-span-4 flex justify-end space-x-3 mt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-search mr-2"></i>
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-times mr-2"></i>
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-credit-card mr-2 text-indigo-600"></i>
                        Payment History
                    </h3>
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full">
                        Total: {{ $payments->total() }} payments
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6">
                                    <span class="font-medium text-gray-900">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-indigo-600 text-sm font-semibold">
                                                {{ substr($payment->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if($payment->booking && $payment->booking->hostel)
                                        <p class="text-sm font-medium text-gray-900">{{ $payment->booking->hostel->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->booking->hostel->location }}</p>
                                    @else
                                        <em class="text-sm text-gray-400">N/A</em>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-sm font-semibold text-green-600">KSh {{ number_format($payment->amount, 2) }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $statusColors = [
                                            'successful' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'cancelled' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusColor = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($payment->transaction_id)
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $payment->transaction_id }}</code>
                                    @else
                                        <em class="text-sm text-gray-400">N/A</em>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->created_at->format('H:i') }}</p>
                                    <p class="text-xs text-gray-400">{{ $payment->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-credit-card text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900 mb-1">No payments found</p>
                                    <p class="text-sm text-gray-500">Payments will appear here once students make transactions.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($payments, 'links'))
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function exportPayments() {
            // Build export URL with current filters
            const url = new URL(window.location.href);
            url.pathname = url.pathname.replace('index', 'export');
            window.location.href = url.toString();
        }

        // Auto-submit form when filters change (optional)
        document.querySelectorAll('select[name="status"], input[name="date_from"], input[name="date_to"]').forEach(element => {
            element.addEventListener('change', function() {
                if (this.name === 'status') {
                    document.querySelector('form').submit();
                }
            });
        });
    </script>
</body>
</html>