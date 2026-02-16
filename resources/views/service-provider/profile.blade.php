<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Service Provider Profile - HostelHub Services</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub Services</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('service-provider.dashboard') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('service-provider.requests') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Service Requests
                        </a>
                        <a href="{{ route('service-provider.profile') }}"
                           class="border-green-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Profile
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ $serviceProvider->company_name }}</span>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Service Provider Profile</h1>
                            <p class="text-gray-600 mt-1">Manage your business information and verification status</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Verification Status Badge -->
                            @if($serviceProvider->is_verified)
                                <div class="bg-green-100 border border-green-200 rounded-full px-4 py-2 flex items-center">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-800 text-sm font-medium">Verified Provider</span>
                                </div>
                            @else
                                <div class="bg-yellow-100 border border-yellow-200 rounded-full px-4 py-2 flex items-center">
                                    <svg class="w-4 h-4 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-yellow-800 text-sm font-medium">Pending Verification</span>
                                </div>
                            @endif

                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Business Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Verification Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Verification Status</h3>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                @if($serviceProvider->is_verified)
                                    <span class="text-green-600">Verified</span>
                                @else
                                    <span class="text-yellow-600">Pending</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Completed Jobs -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Completed Jobs</h3>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $serviceProvider->total_jobs_completed ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Average Rating -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Average Rating</h3>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $serviceProvider->rating ?? '0.0' }}/5.0
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Experience -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Experience</h3>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $serviceProvider->experience_years ?? 0 }} yrs
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('service-provider.update-profile') }}">
                    @csrf

                    <div class="p-6 space-y-8">
                        <!-- Verification Status Alert -->
                        @if(!$serviceProvider->is_verified)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Account Pending Verification</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>Your account is currently under review by our admin team. Once verified, you'll be able to receive service requests from students and landlords.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Account Verified</h3>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p>Your account has been verified! You can now receive service requests and communicate with students and landlords.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Business Information Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Business Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Name -->
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                                    <input type="text" id="company_name" name="company_name"
                                        value="{{ old('company_name', $serviceProvider->company_name) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Service Type -->
                                <div>
                                    <label for="service_type" class="block text-sm font-medium text-gray-700">Primary Service Type *</label>
                                    <select id="service_type" name="service_type" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="">Select Service Type</option>
                                        <option value="wifi_installation" {{ old('service_type', $serviceProvider->service_type) == 'wifi_installation' ? 'selected' : '' }}>WiFi Installation</option>
                                        <option value="plumbing" {{ old('service_type', $serviceProvider->service_type) == 'plumbing' ? 'selected' : '' }}>Plumbing & Water Leakage</option>
                                        <option value="electrical" {{ old('service_type', $serviceProvider->service_type) == 'electrical' ? 'selected' : '' }}>Electrical Repairs</option>
                                        <option value="sewage" {{ old('service_type', $serviceProvider->service_type) == 'sewage' ? 'selected' : '' }}>Sewage & Drainage</option>
                                        <option value="carpentry" {{ old('service_type', $serviceProvider->service_type) == 'carpentry' ? 'selected' : '' }}>Carpentry & Furniture</option>
                                        <option value="cleaning" {{ old('service_type', $serviceProvider->service_type) == 'cleaning' ? 'selected' : '' }}>Deep Cleaning</option>
                                        <option value="pest_control" {{ old('service_type', $serviceProvider->service_type) == 'pest_control' ? 'selected' : '' }}>Pest Control</option>
                                        <option value="other" {{ old('service_type', $serviceProvider->service_type) == 'other' ? 'selected' : '' }}>Other Maintenance</option>
                                    </select>
                                </div>

                                <!-- License Number -->
                                <div>
                                    <label for="license_number" class="block text-sm font-medium text-gray-700">License Number *</label>
                                    <input type="text" id="license_number" name="license_number"
                                        value="{{ old('license_number', $serviceProvider->license_number) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Experience Years -->
                                <div>
                                    <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience *</label>
                                    <input type="number" id="experience_years" name="experience_years" min="0" max="50"
                                        value="{{ old('experience_years', $serviceProvider->experience_years) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Hourly Rate -->
                                <div>
                                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Hourly Rate ($) *</label>
                                    <input type="number" id="hourly_rate" name="hourly_rate" min="0" step="0.01"
                                        value="{{ old('hourly_rate', $serviceProvider->hourly_rate) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Coverage Areas -->
                                <div class="md:col-span-2">
                                    <label for="coverage_areas" class="block text-sm font-medium text-gray-700">Coverage Areas (comma separated)</label>
                                    <input type="text" id="coverage_areas" name="coverage_areas"
                                        value="{{ old('coverage_areas', $serviceProvider->coverage_areas ? implode(', ', $serviceProvider->coverage_areas) : '') }}"
                                        placeholder="e.g., Downtown, Westside, Campus Area"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Business Description Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Business Description</h3>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Service Description *</label>
                                <textarea id="description" name="description" rows="4" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    placeholder="Describe your services, expertise, and what makes your business unique...">{{ old('description', $serviceProvider->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contact Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- User Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Contact Person Name *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                                    <input type="email" id="email" value="{{ $user->email }}" disabled
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 text-gray-500 sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Response Time -->
                                <div>
                                    <label for="response_time" class="block text-sm font-medium text-gray-700">Average Response Time (hours) *</label>
                                    <input type="number" id="response_time" name="response_time" min="1" max="48"
                                        value="{{ old('response_time', $serviceProvider->response_time) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('service-provider.dashboard') }}"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Verification Requirements -->
            @if(!$serviceProvider->is_verified)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Verification Requirements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-700">Valid business license</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-700">Complete profile information</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-700">Professional service description</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-700">Clear contact information</span>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-blue-600">
                        Your profile is currently under review. Our admin team will verify your information within 24-48 hours.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Auto-hide flash messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success messages after 5 seconds
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            }

            // Auto-hide error messages after 8 seconds
            const errorMessage = document.querySelector('.bg-red-100');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 8000);
            }
        });
    </script>
</body>
</html>
