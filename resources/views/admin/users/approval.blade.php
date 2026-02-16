<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bulk User Approval - HostelHub Admin</title>

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
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center px-4 py-3 text-gray-700 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    <a href="{{ route('admin.hostels') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hostels
                    </a>
                    <a href="{{ route('admin.bookings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Bookings
                    </a>
                    <a href="{{ route('admin.service-requests') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Service Requests
                    </a>
                    <a href="{{ route('admin.service-providers') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Service Providers
                    </a>
                    <a href="{{ route('admin.analytics') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
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
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.users') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Users
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Bulk User Approval</h1>
                            <p class="text-gray-600 mt-2">Approve multiple landlord and service provider accounts at once</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                            <i class="fas fa-users mr-1"></i>
                            {{ $pendingUsers->total() }} pending approvals
                        </span>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Bulk Approval Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stats Cards -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">{{ $pendingUsers->total() }}</p>
                            <p class="text-sm text-gray-600">Total Pending</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-home text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $pendingUsers->where('user_type', 'landlord')->count() }}
                            </p>
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
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $pendingUsers->where('user_type', 'service_provider')->count() }}
                            </p>
                            <p class="text-sm text-gray-600">Service Providers</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $pendingUsers->where('created_at', '>=', now()->subDays(7))->count() }}
                            </p>
                            <p class="text-sm text-gray-600">Last 7 Days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Approval Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Pending User Approvals</h3>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600" id="selectedCount">0 users selected</span>
                            <button type="button"
                                    onclick="toggleSelectAll()"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                <i class="fas fa-check-double mr-1"></i>
                                Select All
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.users.bulk-approve') }}" method="POST" id="bulkApproveForm">
                    @csrf

                    <div class="p-6">
                        @if($pendingUsers->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200 bg-gray-50">
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 w-12">
                                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">User Details</th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Type</th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">ID Number</th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Contact</th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Registered</th>
                                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingUsers as $user)
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition duration-150">
                                                <td class="py-4 px-4">
                                                    <input type="checkbox"
                                                           name="user_ids[]"
                                                           value="{{ $user->id }}"
                                                           class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $user->user_type == 'landlord' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}
                                                        capitalize">
                                                        <i class="fas {{ $user->user_type == 'landlord' ? 'fa-home' : 'fa-tools' }} mr-1"></i>
                                                        {{ $user->user_type }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <p class="text-sm text-gray-600">
                                                        {{ $user->id_number ?? 'N/A' }}
                                                    </p>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <p class="text-sm text-gray-600">{{ $user->phone }}</p>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="text-sm text-gray-600">
                                                        <p>{{ $user->created_at->format('M j, Y') }}</p>
                                                        <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center">
                                                            <i class="fas fa-eye mr-1"></i>
                                                            View
                                                        </a>
                                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-green-600 hover:text-green-900 text-sm font-medium flex items-center"
                                                                    onclick="return confirm('Approve {{ $user->name }}? They will receive an approval email.')">
                                                                <i class="fas fa-check mr-1"></i>
                                                                Approve
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bulk Action Buttons -->
                            <div class="mt-6 flex justify-between items-center">
                                <div class="flex space-x-3">
                                    <button type="submit"
                                            id="approveSelectedBtn"
                                            disabled
                                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-medium flex items-center transition duration-150">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Approve Selected Users
                                    </button>
                                    <button type="button"
                                            onclick="clearSelection()"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium flex items-center transition duration-150">
                                        <i class="fas fa-times mr-2"></i>
                                        Clear Selection
                                    </button>
                                </div>

                                <div class="text-sm text-gray-600">
                                    Showing {{ $pendingUsers->firstItem() }} to {{ $pendingUsers->lastItem() }} of {{ $pendingUsers->total() }} results
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Approvals</h3>
                                <p class="text-gray-500 max-w-md mx-auto mb-6">
                                    All landlord and service provider accounts are currently approved. New registration requests will appear here automatically.
                                </p>
                                <a href="{{ route('admin.users') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Users Management
                                </a>
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Pagination -->
                @if($pendingUsers->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pendingUsers->links() }}
                    </div>
                @endif
            </div>

            <!-- Information Card -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900">About Bulk Approval</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            When you approve users in bulk, each selected user will:
                        </p>
                        <ul class="text-sm text-blue-700 mt-2 list-disc list-inside space-y-1">
                            <li>Receive an approval email notification</li>
                            <li>Have their account activated immediately</li>
                            <li>Gain access to all platform features based on their user type</li>
                            <li>For service providers, their profile will be marked as verified</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const approveBtn = document.getElementById('approveSelectedBtn');
            const selectedCount = document.getElementById('selectedCount');
            const bulkForm = document.getElementById('bulkApproveForm');

            // Select all functionality
            function toggleSelectAll() {
                const allChecked = Array.from(userCheckboxes).every(checkbox => checkbox.checked);
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                updateSelection();
            }

            selectAll.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelection();
            });

            // Update selection count
            function updateSelection() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                const count = selected.length;

                approveBtn.disabled = count === 0;
                selectedCount.textContent = `${count} user${count !== 1 ? 's' : ''} selected`;

                // Update select all checkbox state
                selectAll.checked = count > 0 && count === userCheckboxes.length;
                selectAll.indeterminate = count > 0 && count < userCheckboxes.length;
            }

            // Clear selection
            function clearSelection() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelection();
            }

            // Add event listeners to individual checkboxes
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelection);
            });

            // Form submission confirmation
            bulkForm.addEventListener('submit', function(e) {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                const count = selected.length;

                if (count > 0) {
                    if (!confirm(`Are you sure you want to approve ${count} user${count !== 1 ? 's' : ''}? This will send approval emails to all selected users and activate their accounts immediately.`)) {
                        e.preventDefault();
                    }
                } else {
                    e.preventDefault();
                    alert('Please select at least one user to approve.');
                }
            });

            // Initialize selection count
            updateSelection();
        });
    </script>
</body>
</html>
