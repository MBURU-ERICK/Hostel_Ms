<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $user->name }} - User Details - HostelHub Admin</title>

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
                        <a href="{{ route('admin.users') }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-arrow-left"></i>
                            Back to Users
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-600 mt-2">User Details & Activity</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.users.bulk-approval') }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            Bulk Approve Users
                        </a>
                        <button onclick="toggleEditForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-edit mr-2"></i>
                            Edit User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - User Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- User Profile Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                                    <p class="text-gray-900">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                                    <p class="text-gray-900">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Phone Number</label>
                                    <p class="text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">User Type</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->user_type == 'student' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $user->user_type == 'landlord' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $user->user_type == 'service_provider' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $user->user_type == 'admin' ? 'bg-red-100 text-red-800' : '' }}
                                        capitalize">
                                        {{ $user->user_type }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Account Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Approval Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $user->is_approved ? 'Approved' : 'Pending Approval' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Email Verified</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Member Since</label>
                                    <p class="text-gray-900">{{ $user->created_at->format('F j, Y') }}</p>
                                </div>
                            </div>

                            @if($user->studentProfile)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-md font-semibold text-gray-900 mb-4">Student Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Admission Number</label>
                                        <p class="text-gray-900">{{ $user->studentProfile->admission_number }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">ID Number</label>
                                        <p class="text-gray-900">{{ $user->studentProfile->id_number }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                                        <p class="text-gray-900 capitalize">{{ $user->studentProfile->gender }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Institution</label>
                                        <p class="text-gray-900">{{ $user->studentProfile->institution_name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Course</label>
                                        <p class="text-gray-900">{{ $user->studentProfile->course }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Year of Study</label>
                                        <p class="text-gray-900">{{ $user->studentProfile->year_of_study }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Edit User Form (Hidden by Default) -->
                    <div id="editForm" class="bg-white rounded-lg shadow-sm border border-gray-200 hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Edit User Information</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                                        <select id="user_type" name="user_type"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="student" {{ $user->user_type == 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="landlord" {{ $user->user_type == 'landlord' ? 'selected' : '' }}>Landlord</option>
                                            <option value="service_provider" {{ $user->user_type == 'service_provider' ? 'selected' : '' }}>Service Provider</option>
                                            <option value="admin" {{ $user->user_type == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_active" value="1"
                                                   {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">Active Account</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_approved" value="1"
                                                   {{ $user->is_approved ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700">Approved</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-6 flex space-x-3">
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                        Update User
                                    </button>
                                    <button type="button" onclick="toggleEditForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- User Activity -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">User Activity</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-600">{{ $user->bookings_count }}</p>
                                    <p class="text-sm text-gray-600">Total Bookings</p>
                                </div>
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <p class="text-2xl font-bold text-green-600">{{ $user->service_requests_count }}</p>
                                    <p class="text-sm text-gray-600">Service Requests</p>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg">
                                    <p class="text-2xl font-bold text-purple-600">{{ $user->reviews_count ?? 0 }}</p>
                                    <p class="text-sm text-gray-600">Reviews</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Stats -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if(!$user->is_approved)
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline w-full">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                        <i class="fas fa-check mr-2"></i>Approve User
                                    </button>
                                </form>
                            @endif

                            @if($user->is_active)
                                <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" class="inline w-full">
                                    @csrf
                                    <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm">
                                        <i class="fas fa-pause mr-2"></i>Suspend User
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="inline w-full">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                        <i class="fas fa-play mr-2"></i>Activate User
                                    </button>
                                </form>
                            @endif

                            @if($user->user_type === 'student' && $user->studentProfile)
                                <a href="#" class="block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm text-center">
                                    <i class="fas fa-graduation-cap mr-2"></i>View Student Profile
                                </a>
                            @endif

                            <!-- Delete User Button -->
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline w-full"
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                                    <i class="fas fa-trash mr-2"></i>Delete User
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Bulk Approval Quick Stats -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Bulk Approval</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-indigo-600">
                                        {{ \App\Models\User::where('is_approved', false)->whereIn('user_type', ['landlord', 'service_provider'])->count() }}
                                    </p>
                                    <p class="text-sm text-gray-600">Pending Approvals</p>
                                </div>
                                <a href="{{ route('admin.users.bulk-approval') }}"
                                   class="block w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition text-center">
                                    <i class="fas fa-users mr-2"></i>
                                    Manage Bulk Approvals
                                </a>
                                <p class="text-xs text-gray-500 text-center">
                                    Approve multiple landlord and service provider accounts at once
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Account Information</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Last Login</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Profile Updated</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $user->updated_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Email Verified</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'Not Verified' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEditForm() {
            const editForm = document.getElementById('editForm');
            editForm.classList.toggle('hidden');
        }

        // Auto-hide success messages after 5 seconds
        setTimeout(() => {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
