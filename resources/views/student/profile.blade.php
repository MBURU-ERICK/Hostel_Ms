<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Profile - Hostel Management System</title>

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
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('student.dashboard') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('student.search-hostels') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Search Hostels
                        </a>
                        <a href="{{ route('student.profile') }}"
                           class="border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Profile
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>

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
                            <h1 class="text-2xl font-bold text-gray-900">Student Profile</h1>
                            <p class="text-gray-600 mt-1">Manage your personal and academic information</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
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

            <!-- Profile Form -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('student.update-profile') }}">
                    @csrf

                    <div class="p-6 space-y-8">
                        <!-- Personal Information Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Personal Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                                    <select id="gender" name="gender" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->studentProfile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->studentProfile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->studentProfile->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Academic Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Admission Number -->
                                <div>
                                    <label for="admission_number" class="block text-sm font-medium text-gray-700">Admission Number *</label>
                                    <input type="text" id="admission_number" name="admission_number"
                                        value="{{ old('admission_number', $user->studentProfile->admission_number ?? '') }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- ID Number -->
                                <div>
                                    <label for="id_number" class="block text-sm font-medium text-gray-700">ID Number *</label>
                                    <input type="text" id="id_number" name="id_number"
                                        value="{{ old('id_number', $user->studentProfile->id_number ?? '') }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Institution Name -->
                                <div class="md:col-span-2">
                                    <label for="institution_name" class="block text-sm font-medium text-gray-700">Institution Name *</label>
                                    <input type="text" id="institution_name" name="institution_name"
                                        value="{{ old('institution_name', $user->studentProfile->institution_name ?? '') }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Course -->
                                <div>
                                    <label for="course" class="block text-sm font-medium text-gray-700">Course/Program *</label>
                                    <input type="text" id="course" name="course"
                                        value="{{ old('course', $user->studentProfile->course ?? '') }}" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Year of Study -->
                                <div>
                                    <label for="year_of_study" class="block text-sm font-medium text-gray-700">Year of Study *</label>
                                    <select id="year_of_study" name="year_of_study" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Select Year</option>
                                        <option value="1st Year" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                        <option value="2nd Year" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                        <option value="3rd Year" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                        <option value="4th Year" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                        <option value="5th Year" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                        <option value="Postgraduate" {{ old('year_of_study', $user->studentProfile->year_of_study ?? '') == 'Postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Emergency Contact (Optional)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Emergency Contact Name -->
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Contact Person Name</label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                        value="{{ old('emergency_contact_name', $user->studentProfile->emergency_contact_name ?? '') }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                <!-- Emergency Contact Phone -->
                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone Number</label>
                                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone', $user->studentProfile->emergency_contact_phone ?? '') }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Address Information (Optional)</h3>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Home Address</label>
                                <textarea id="address" name="address" rows="3"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('address', $user->studentProfile->address ?? '') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t">
                            <a href="{{ route('student.dashboard') }}"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Profile Status Card -->
            @if($user->studentProfile)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Profile Complete</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Your student profile is complete and ready for hostel bookings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Profile Incomplete</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Please complete your student profile to access all features including hostel bookings.</p>
                            </div>
                        </div>
                    </div>
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
