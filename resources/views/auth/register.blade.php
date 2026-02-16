<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Hostel Management System - Register</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .form-section {
            display: none;
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div>
                <div class="flex justify-center">
                    <div class="bg-blue-600 p-3 rounded-full">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Join our platform in just a few steps
                </p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were {{ $errors->count() }} errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf

                <div class="space-y-4">
                    <!-- User Type Dropdown -->
                    <div>
                        <label for="user_type" class="block text-sm font-medium text-gray-700">Account Type</label>
                        <select id="user_type" name="user_type" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-300"
                            onchange="handleUserTypeChange()">
                            <option value="">Select Account Type</option>
                            <option value="student" {{ old('user_type') == 'student' ? 'selected' : '' }}>🎓 Student</option>
                            <option value="landlord" {{ old('user_type') == 'landlord' ? 'selected' : '' }}>🏠 Landlord</option>
                            <option value="service_provider" {{ old('user_type') == 'service_provider' ? 'selected' : '' }}>🔧 Service Provider</option>
                        </select>
                        <p id="user_type_description" class="mt-1 text-xs text-gray-500">
                            Please select your account type to continue
                        </p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="Enter your full name">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="Enter your email address">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="e.g., 0712345678">
                    </div>

                    <!-- ID Number Field (for landlords and service providers) -->
                    <div id="id_number_field" style="display: none;">
                        <label for="id_number" class="block text-sm font-medium text-gray-700">ID Number <span class="text-red-500">*</span></label>
                        <input id="id_number" name="id_number" type="text" value="{{ old('id_number') }}"
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="Enter your national ID number">
                        <p class="mt-1 text-xs text-gray-500">Required for landlord and service provider verification</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="Create a strong password">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition duration-300"
                            placeholder="Confirm your password">
                    </div>
                </div>

                <!-- Account Type Information -->
                <div id="account_info" class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                    <h3 class="text-sm font-medium text-blue-800 mb-2" id="account_title">Account Information</h3>
                    <p class="text-xs text-blue-700" id="account_description">
                        Please fill in all the required information for your selected account type.
                    </p>
                    <p class="text-xs text-blue-600 mt-1" id="approval_info"></p>
                </div>

                <!-- Terms and Conditions -->
                <div class="text-xs text-gray-600">
                    <p>By creating an account, you agree to our <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>.</p>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Already have an account? Sign in
                        </a>
                    </div>

                    <button type="submit"
                        class="group relative flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleUserTypeChange() {
            const userType = document.getElementById('user_type').value;
            const idNumberField = document.getElementById('id_number_field');
            const idNumberInput = document.getElementById('id_number');
            const accountInfo = document.getElementById('account_info');
            const accountTitle = document.getElementById('account_title');
            const accountDescription = document.getElementById('account_description');
            const approvalInfo = document.getElementById('approval_info');
            const userTypeDescription = document.getElementById('user_type_description');

            // Reset all fields first
            idNumberField.style.display = 'none';
            idNumberInput.required = false;
            accountInfo.style.display = 'none';

            if (userType === '') {
                userTypeDescription.textContent = 'Please select your account type to continue';
                return;
            }

            // Show account info based on user type
            accountInfo.style.display = 'block';

            if (userType === 'student') {
                accountTitle.textContent = '🎓 Student Account';
                accountDescription.textContent = 'Find hostels, book accommodation, and access student services. Perfect for students looking for comfortable and affordable housing.';
                approvalInfo.textContent = '✅ Student accounts are activated immediately after registration.';
                userTypeDescription.textContent = 'Ideal for students seeking accommodation near their educational institutions';

                // Hide ID number field for students
                idNumberField.style.display = 'none';
                idNumberInput.required = false;

            } else if (userType === 'landlord') {
                accountTitle.textContent = '🏠 Landlord Account';
                accountDescription.textContent = 'List and manage your hostels, connect with students, and handle bookings efficiently.';
                approvalInfo.textContent = '⏳ Landlord accounts require admin approval. You will be notified once your account is activated.';
                userTypeDescription.textContent = 'For property owners who want to list and manage their hostels';

                // Show ID number field for landlords
                idNumberField.style.display = 'block';
                idNumberInput.required = true;

            } else if (userType === 'service_provider') {
                accountTitle.textContent = '🔧 Service Provider Account';
                accountDescription.textContent = 'Offer maintenance and other services to hostels. Connect with landlords and students for service requests.';
                approvalInfo.textContent = '⏳ Service Provider accounts require admin approval. You will be notified once your account is activated.';
                userTypeDescription.textContent = 'For professionals offering maintenance and other services to hostels';

                // Show ID number field for service providers
                idNumberField.style.display = 'block';
                idNumberInput.required = true;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const userType = document.getElementById('user_type');

            // Trigger change event if a value is already selected (e.g., from validation errors)
            if (userType.value) {
                handleUserTypeChange();
            }

            // Add smooth transitions for better UX
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
                });

                input.addEventListener('blur', function() {
                    this.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
                });
            });
        });

        // Form validation enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const userType = document.getElementById('user_type').value;
            const idNumberInput = document.getElementById('id_number');

            if ((userType === 'landlord' || userType === 'service_provider') && !idNumberInput.value.trim()) {
                e.preventDefault();
                alert('Please provide your ID number for verification.');
                idNumberInput.focus();
                return false;
            }

            return true;
        });
    </script>
</body>
</html>
