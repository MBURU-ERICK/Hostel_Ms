<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>New Service Request - HostelHub</title>

    <script src="https://cdn.tailwindcss.com"></script>
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
                        <span class="ml-2 text-xl font-bold text-gray-900">HostelHub Student</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('student.services.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Services
                </a>
                <h1 class="text-3xl font-bold text-gray-900">New Service Request</h1>
                <p class="text-gray-600 mt-2">Fill out the form below to request maintenance services</p>
            </div>

            <!-- Service Request Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Service Request Details</h2>
                </div>

                <form action="{{ route('student.services.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Service Type -->
                        <div>
                            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Service Type *
                            </label>
                            <select id="service_type" name="service_type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a service type</option>
                                @foreach($serviceTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('service_type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hostel Selection -->
                        <div>
                            <label for="hostel_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Hostel *
                            </label>
                            <select id="hostel_id" name="hostel_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select your hostel</option>
                                @foreach($hostels as $hostel)
                                    <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                        {{ $hostel->name }} - {{ $hostel->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hostel_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Issue Title *
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                placeholder="Brief description of the issue"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Detailed Description *
                            </label>
                            <textarea id="description" name="description" rows="4" required
                                placeholder="Please provide detailed information about the issue..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Urgency Level -->
                        <div>
                            <label for="urgency_level" class="block text-sm font-medium text-gray-700 mb-2">
                                Urgency Level *
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="urgency_level" value="low" {{ old('urgency_level') == 'low' ? 'checked' : '' }} class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-3 border border-gray-300 rounded-lg hover:border-blue-500">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                                            <span class="text-sm">Low</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="urgency_level" value="medium" {{ old('urgency_level') == 'medium' ? 'checked' : '' }} class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-3 border border-gray-300 rounded-lg hover:border-blue-500">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                                            <span class="text-sm">Medium</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="urgency_level" value="high" {{ old('urgency_level') == 'high' ? 'checked' : '' }} class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-3 border border-gray-300 rounded-lg hover:border-blue-500">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                                            <span class="text-sm">High</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="urgency_level" value="emergency" {{ old('urgency_level') == 'emergency' ? 'checked' : '' }} class="sr-only" required>
                                    <div class="flex items-center justify-center w-full p-3 border border-gray-300 rounded-lg hover:border-blue-500">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                                            <span class="text-sm">Emergency</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('urgency_level')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address and Room Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Address *
                                </label>
                                <input type="text" id="address" name="address" value="{{ old('address') }}" required
                                    placeholder="Hostel address and location details"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Room Number *
                                </label>
                                <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                                    placeholder="Your room number"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('room_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('student.services.index') }}"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Information -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800">What happens next?</h4>
                        <ul class="text-sm text-blue-700 mt-1 list-disc list-inside space-y-1">
                            <li>Your request will be sent to available service providers</li>
                            <li>Service providers will contact you to schedule the service</li>
                            <li>You'll receive updates on your request status</li>
                            <li>After completion, you can rate the service quality</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactivity to the urgency level selection
        document.querySelectorAll('input[name="urgency_level"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label.relative').forEach(label => {
                    label.classList.remove('border-blue-500', 'bg-blue-50');
                });
                if (this.checked) {
                    this.closest('label').classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });

        // Initialize selected urgency level
        document.querySelectorAll('input[name="urgency_level"]').forEach(radio => {
            if (radio.checked) {
                radio.closest('label').classList.add('border-blue-500', 'bg-blue-50');
            }
        });
    </script>
</body>
</html>
