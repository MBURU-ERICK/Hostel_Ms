<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Service Provider Setup - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div>
                <div class="flex justify-center">
                    <div class="bg-green-600 p-3 rounded-full">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Complete Your Service Provider Profile
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Set up your service provider account to start receiving maintenance requests
                </p>
            </div>

            <!-- Setup Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('service-provider.store-setup') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name *
                            </label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            @error('company_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Primary Service Type *
                            </label>
                            <select id="service_type" name="service_type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select your main service</option>
                                <option value="wifi_installation" {{ old('service_type') == 'wifi_installation' ? 'selected' : '' }}>WiFi Installation</option>
                                <option value="plumbing" {{ old('service_type') == 'plumbing' ? 'selected' : '' }}>Plumbing & Water Leakage</option>
                                <option value="electrical" {{ old('service_type') == 'electrical' ? 'selected' : '' }}>Electrical Repairs</option>
                                <option value="sewage" {{ old('service_type') == 'sewage' ? 'selected' : '' }}>Sewage & Drainage</option>
                                <option value="carpentry" {{ old('service_type') == 'carpentry' ? 'selected' : '' }}>Carpentry & Furniture</option>
                                <option value="cleaning" {{ old('service_type') == 'cleaning' ? 'selected' : '' }}>Deep Cleaning</option>
                                <option value="pest_control" {{ old('service_type') == 'pest_control' ? 'selected' : '' }}>Pest Control</option>
                                <option value="other" {{ old('service_type') == 'other' ? 'selected' : '' }}>Other Maintenance</option>
                            </select>
                            @error('service_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Service Description *
                            </label>
                            <textarea id="description" name="description" rows="4" required
                                placeholder="Describe your services, expertise, and what students can expect..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div>
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Business License Number
                            </label>
                            <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}"
                                placeholder="Optional - for verification purposes"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            @error('license_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Experience and Rates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">
                                    Years of Experience *
                                </label>
                                <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" required min="0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                @error('experience_years')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hourly Rate (Ksh) *
                                </label>
                                <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" required min="0" step="0.01"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                @error('hourly_rate')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 border-t border-gray-200">
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Complete Setup
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">Verification Process</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Your profile will be reviewed for verification. Verified service providers get priority in service requests and gain student trust through ratings and reviews.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
