<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Make Payment - Hostel Management System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233B82F6'><path d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'/></svg>">
    
    <!-- Development Mode Indicator -->
    <style>
        .dev-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        
        /* Invoice Styles */
        .invoice-card {
            transition: all 0.3s ease;
        }
        
        .invoice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Development Mode Badge -->
    <div class="dev-badge">
        🚀 DEVELOPMENT MODE - M-Pesa Simulator Active
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.my-bookings') }}"
                       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Bookings
                    </a>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h1>
                <p class="text-lg text-gray-600">Secure payment via M-Pesa for your hostel booking</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Form Header -->
                        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                            <h2 class="text-xl font-semibold text-white">Payment Details</h2>
                        </div>

                        <div class="p-6">
                            <!-- Booking Summary -->
                            <div class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-900">Booking Summary</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Hostel:</span>
                                        <span class="font-semibold text-gray-900">{{ $booking->hostel->name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Room Type:</span>
                                        <span class="font-medium">{{ $booking->room_type ?? 'Standard' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Duration:</span>
                                        <span class="font-medium">{{ $booking->duration_months }} month{{ $booking->duration_months > 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Check-in:</span>
                                        <span class="font-medium">{{ $booking->check_in_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-3 mt-3">
                                        <div class="flex justify-between items-center text-base font-semibold">
                                            <span class="text-gray-900">Total Amount:</span>
                                            <span class="text-green-600 text-lg">KSh {{ number_format($booking->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Form -->
                            <form id="paymentForm" class="space-y-6">
                                @csrf

                                <!-- Phone Input -->
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        M-Pesa Phone Number
                                    </label>
                                    <div class="flex rounded-lg shadow-sm">
                                        <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-medium">
                                            +254
                                        </span>
                                        <input type="tel"
                                               id="phone_number"
                                               name="phone_number"
                                               required
                                               pattern="[0-9]{9}"
                                               placeholder="712345678"
                                               maxlength="9"
                                               class="flex-1 block w-full px-4 py-3 rounded-r-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                               value="{{ old('phone_number', Auth::user()->phone ? substr(Auth::user()->phone, -9) : '712345678') }}">
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Enter your M-Pesa registered phone number
                                    </div>
                                    <div id="phoneError" class="mt-1 text-sm text-red-600 hidden"></div>
                                </div>

                                <!-- Development Mode Options -->
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                        <h3 class="font-semibold text-purple-900">Development Mode - Simulate Payment</h3>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="flex items-center space-x-3">
                                            <input type="radio" name="payment_simulation" value="success" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                            <span class="text-sm text-gray-700">Success (Payment will complete and update all systems)</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="radio" name="payment_simulation" value="pending" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                            <span class="text-sm text-gray-700">Pending (User hasn't entered PIN)</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="radio" name="payment_simulation" value="failed" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                            <span class="text-sm text-gray-700">Failed (Insufficient funds)</span>
                                        </label>
                                        <label class="flex items-center space-x-3">
                                            <input type="radio" name="payment_simulation" value="cancelled" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                                            <span class="text-sm text-gray-700">Cancelled (User cancelled)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit"
                                        id="payButton"
                                        class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-4 px-6 rounded-lg font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-green-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Pay KSh {{ number_format($booking->total_amount, 2) }} via M-Pesa
                                </button>
                            </form>

                            <!-- Payment Status -->
                            <div id="paymentStatus" class="hidden mt-6 animate-fade-in">
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div id="statusSpinner" class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="font-semibold text-blue-900 text-lg" id="statusTitle">Processing Payment</h4>
                                            <p class="text-blue-700 mt-1" id="statusMessage">
                                                A payment request has been sent to your phone. Please enter your M-Pesa PIN to complete the payment.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Payment status will update in</span>
                                        <span class="text-sm font-semibold text-gray-900" id="countdown">60s</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div id="progressBar" class="bg-green-500 h-2.5 rounded-full transition-all duration-1000 ease-linear" style="width: 100%"></div>
                                    </div>
                                </div>

                                <!-- Status Timeline -->
                                <div class="mt-4 border-t border-gray-200 pt-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm" id="step1">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span class="text-gray-600">Payment request sent to phone</span>
                                        </div>
                                        <div class="flex items-center text-sm" id="step2">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span class="text-gray-600">Waiting for PIN entry</span>
                                        </div>
                                        <div class="flex items-center text-sm" id="step3">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span class="text-gray-600">Processing payment</span>
                                        </div>
                                        <div class="flex items-center text-sm" id="step4">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span class="text-gray-600">Confirming transaction</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Section (Shown after successful payment) -->
                            <div id="invoiceSection" class="hidden mt-8 animate-fade-in">
                                <div class="bg-white border-2 border-green-200 rounded-xl overflow-hidden invoice-card">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex justify-between items-center">
                                        <h3 class="text-xl font-semibold text-white flex items-center">
                                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Payment Invoice
                                        </h3>
                                        <span class="bg-white text-green-600 text-xs font-bold px-3 py-1 rounded-full">PAID</span>
                                    </div>
                                    <div class="p-6" id="invoiceContent">
                                        <!-- Invoice content will be dynamically inserted here -->
                                    </div>
                                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                                        <button onclick="downloadInvoice()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download Invoice
                                        </button>
                                        <a href="{{ route('student.my-bookings') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                            View My Bookings
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Instructions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Instructions</h3>
                        </div>
                        <div class="p-5">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <span class="text-green-600 text-sm font-bold">1</span>
                                    </div>
                                    <p class="ml-3 text-sm text-gray-600">Enter your M-Pesa registered phone number</p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <span class="text-green-600 text-sm font-bold">2</span>
                                    </div>
                                    <p class="ml-3 text-sm text-gray-600">Click "Pay via M-Pesa" button</p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <span class="text-green-600 text-sm font-bold">3</span>
                                    </div>
                                    <p class="ml-3 text-sm text-gray-600">Check your phone for STK Push notification</p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <span class="text-green-600 text-sm font-bold">4</span>
                                    </div>
                                    <p class="ml-3 text-sm text-gray-600">Enter your M-Pesa PIN when prompted</p>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <span class="text-green-600 text-sm font-bold">5</span>
                                    </div>
                                    <p class="ml-3 text-sm text-gray-600">Wait for payment confirmation</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Badge -->
                    <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
                        <div class="bg-green-50 px-5 py-4 border-b border-green-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900">Secure Payment</h3>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2 text-sm text-gray-600">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Encrypted transaction
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    M-Pesa secured
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Instant confirmation
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Help Information -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-yellow-800">Important Notes</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Ensure sufficient funds in your M-Pesa account</li>
                                        <li>Keep your phone nearby to enter PIN</li>
                                        <li>Do not close this page until complete</li>
                                        <li>Contact support for any issues</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced phone number validation
        function validatePhoneNumber(phone) {
            const cleanPhone = phone.replace(/\D/g, '');
            if (cleanPhone.length !== 9) {
                return { isValid: false, message: 'Phone number must be 9 digits' };
            }
            if (!/^[17]\d{8}$/.test(cleanPhone)) {
                return { isValid: false, message: 'Phone number must start with 1 or 7' };
            }
            return { isValid: true, formatted: '254' + cleanPhone };
        }

        // Show error message
        function showError(message) {
            const errorDiv = document.getElementById('phoneError');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        // Hide error message
        function hideError() {
            const errorDiv = document.getElementById('phoneError');
            errorDiv.classList.add('hidden');
        }

        // Real-time phone validation
        document.getElementById('phone_number').addEventListener('input', function(e) {
            const value = e.target.value;
            if (value.length === 9) {
                const validation = validatePhoneNumber(value);
                if (!validation.isValid) {
                    showError(validation.message);
                } else {
                    hideError();
                }
            } else {
                hideError();
            }
        });

        // Generate Invoice HTML
        function generateInvoice(paymentData) {
            const date = new Date();
            const invoiceNumber = 'INV-' + date.getFullYear() + 
                                 String(date.getMonth() + 1).padStart(2, '0') + 
                                 String(date.getDate()).padStart(2, '0') + 
                                 '-' + Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            
            return `
                <div class="space-y-4">
                    <!-- Invoice Header -->
                    <div class="flex justify-between items-start border-b border-gray-200 pb-4">
                        <div>
                            <h4 class="text-2xl font-bold text-gray-900">INVOICE</h4>
                            <p class="text-sm text-gray-600 mt-1">#${invoiceNumber}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Date: ${date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p class="text-sm text-gray-600">Time: ${date.toLocaleTimeString()}</p>
                        </div>
                    </div>
                    
                    <!-- Company Details -->
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-900">Hostel Management System</p>
                            <p class="text-sm text-gray-600">123 Hostel Avenue</p>
                            <p class="text-sm text-gray-600">Nairobi, Kenya</p>
                            <p class="text-sm text-gray-600">Tel: +254 700 000000</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">Bill To:</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            <p class="text-sm text-gray-600">Tel: ${paymentData.phoneNumber}</p>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    <div class="border-t border-b border-gray-200 py-4 my-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="text-left py-2 px-3">Description</th>
                                    <th class="text-right py-2 px-3">Amount (KSh)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-2 px-3">Hostel: {{ $booking->hostel->name }}</td>
                                    <td class="text-right py-2 px-3">{{ number_format($booking->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3">Room Type: {{ $booking->room_type ?? 'Standard' }}</td>
                                    <td class="text-right py-2 px-3"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3">Duration: {{ $booking->duration_months }} month(s)</td>
                                    <td class="text-right py-2 px-3"></td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3">Check-in: {{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td class="text-right py-2 px-3"></td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50 font-semibold">
                                <tr>
                                    <td class="py-3 px-3">Total</td>
                                    <td class="text-right py-3 px-3 text-green-600">KSh {{ number_format($booking->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Payment Info -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Payment Method:</p>
                            <p class="font-semibold text-gray-900">M-Pesa</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Transaction ID:</p>
                            <p class="font-semibold text-gray-900">${paymentData.transactionId || 'MP' + Math.random().toString(36).substring(2, 10).toUpperCase()}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">M-Pesa Reference:</p>
                            <p class="font-semibold text-gray-900">${paymentData.mpesaRef || 'REF' + Math.floor(Math.random() * 1000000000)}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Payment Status:</p>
                            <p class="font-semibold text-green-600">Paid</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="border-t border-gray-200 pt-4 mt-4 text-center text-sm text-gray-500">
                        <p>Thank you for your payment! This invoice serves as your payment receipt.</p>
                        <p class="mt-1">For any queries, please contact our support team.</p>
                    </div>
                </div>
            `;
        }

        // Download Invoice as HTML file
        function downloadInvoice() {
            const invoiceContent = document.getElementById('invoiceContent').innerHTML;
            const style = `
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
                    .invoice-card { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
                </style>
            `;
            const fullHtml = `
                <!DOCTYPE html>
                <html>
                <head><title>Payment Invoice</title>${style}</head>
                <body>
                    <div class="invoice-card">${invoiceContent}</div>
                </body>
                </html>
            `;
            
            const blob = new Blob([fullHtml], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `invoice-${new Date().getTime()}.html`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Update status timeline
        function updateTimeline(step) {
            const steps = ['step1', 'step2', 'step3', 'step4'];
            steps.forEach((stepId, index) => {
                const element = document.getElementById(stepId);
                const text = element.querySelector('span').textContent;
                if (index < step) {
                    element.innerHTML = '<svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="text-green-700">' + text + '</span>';
                } else {
                    element.innerHTML = '<svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg><span class="text-gray-600">' + text + '</span>';
                }
            });
        }

        // Simulate payment and update all systems
        async function simulatePayment(simulationType, phoneNumber) {
            return new Promise((resolve) => {
                let currentStep = 1;
                updateTimeline(currentStep);
                
                // Create payment record via API
                fetch(`/student/payment/{{ $booking->id }}/initiate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber,
                        simulation: true,
                        simulation_type: simulationType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Payment initiated:', data);
                })
                .catch(error => {
                    console.error('Error initiating payment:', error);
                });

                // Simulate status updates
                let count = 0;
                const statusMessages = {
                    success: [
                        { title: 'Processing Payment', message: 'Payment request sent to your phone. Please check your phone for M-Pesa prompt.' },
                        { title: 'PIN Entered', message: 'PIN received. Processing your payment...' },
                        { title: 'Payment Successful', message: 'Payment completed successfully! Updating all systems...' },
                        { title: 'All Systems Updated', message: 'Payment confirmed. Invoice generated. All systems updated.' }
                    ],
                    pending: [
                        { title: 'Processing Payment', message: 'Payment request sent to your phone. Please check your phone for M-Pesa prompt.' },
                        { title: 'Waiting for PIN', message: 'Waiting for you to enter your M-Pesa PIN...' },
                        { title: 'Still Waiting', message: 'Please enter your PIN to complete the transaction.' },
                        { title: 'Timeout', message: 'Payment request expired. Please try again.' }
                    ],
                    failed: [
                        { title: 'Processing Payment', message: 'Payment request sent to your phone.' },
                        { title: 'Transaction Failed', message: 'Insufficient funds in your M-Pesa account.' },
                        { title: 'Payment Failed', message: 'Transaction failed. Please try again.' },
                        { title: 'Update Failed', message: 'Payment could not be processed.' }
                    ],
                    cancelled: [
                        { title: 'Processing Payment', message: 'Payment request sent to your phone.' },
                        { title: 'Transaction Cancelled', message: 'You cancelled the transaction on your phone.' },
                        { title: 'Payment Cancelled', message: 'Transaction was cancelled by user.' },
                        { title: 'Cancellation Confirmed', message: 'Payment has been cancelled.' }
                    ]
                };

                const interval = setInterval(() => {
                    if (count < statusMessages[simulationType].length) {
                        const msg = statusMessages[simulationType][count];
                        updateStatusUI(
                            simulationType === 'success' ? 'green' : 
                            simulationType === 'pending' ? 'blue' :
                            simulationType === 'failed' ? 'red' : 'yellow',
                            msg.title,
                            msg.message
                        );
                        currentStep++;
                        updateTimeline(currentStep);
                        count++;
                    } else {
                        clearInterval(interval);
                        resolve(simulationType);
                    }
                }, 2000);
            });
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const payButton = document.getElementById('payButton');
            const phoneInput = document.getElementById('phone_number');
            const simulationType = document.querySelector('input[name="payment_simulation"]:checked').value;

            // Get the phone number and clean it
            let phoneNumber = phoneInput.value.trim();
            phoneNumber = phoneNumber.replace(/[^\d]/g, '');

            // Validate basic format
            if (!phoneNumber.match(/^(0|7|1)\d{8}$/)) {
                alert('Please enter a valid Kenyan phone number (e.g., 0712345678 or 712345678)');
                phoneInput.focus();
                return;
            }

            // Format phone number for storage
            const formattedPhone = '254' + phoneNumber.replace(/^0/, '');

            // Disable pay button and show loading
            payButton.disabled = true;
            payButton.innerHTML = `
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white mr-2"></div>
                Processing Payment...
            `;

            // Show payment status
            const paymentStatus = document.getElementById('paymentStatus');
            paymentStatus.classList.remove('hidden');

            // Start countdown
            let countdown = 59;
            const countdownElement = document.getElementById('countdown');
            const progressBar = document.getElementById('progressBar');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');

            // Update initial status
            statusTitle.textContent = 'Initiating Payment';
            statusMessage.textContent = 'Sending payment request to your phone...';

            // Simulate API call delay
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Start countdown and simulate payment
            const interval = setInterval(async () => {
                countdown--;
                countdownElement.textContent = countdown + 's';
                progressBar.style.width = `${(countdown / 59) * 100}%`;

                // Update status based on countdown
                if (countdown === 45) {
                    updateTimeline(2);
                    statusTitle.textContent = 'Check Your Phone';
                    statusMessage.textContent = 'Please enter your M-Pesa PIN when prompted.';
                } else if (countdown === 30) {
                    updateTimeline(3);
                } else if (countdown === 15) {
                    updateTimeline(4);
                }

                if (countdown <= 0) {
                    clearInterval(interval);
                    
                    // Handle final status based on simulation type
                    const result = await simulatePayment(simulationType, formattedPhone);
                    
                    if (result === 'success') {
                        // Update payment status in database via API
                        try {
                            const updateResponse = await fetch('/student/payment/update-status', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    booking_id: {{ $booking->id }},
                                    status: 'successful',
                                    transaction_id: 'MP' + Math.random().toString(36).substring(2, 10).toUpperCase(),
                                    completed_at: new Date().toISOString()
                                })
                            });
                            
                            const updateData = await updateResponse.json();
                            console.log('Payment status updated:', updateData);
                            
                            // Show success message with broadcast info
                            const statusSpinner = document.getElementById('statusSpinner');
                            statusSpinner.classList.remove('animate-spin', 'border-b-2');
                            statusSpinner.innerHTML = '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                            
                            updateStatusUI('green', 'Payment Successful!', 'Your payment has been processed successfully. All systems have been updated.');
                            
                            // Generate and show invoice
                            const paymentData = {
                                phoneNumber: formattedPhone,
                                transactionId: updateData.transaction_id || 'MP' + Math.random().toString(36).substring(2, 10).toUpperCase(),
                                mpesaRef: 'REF' + Math.floor(Math.random() * 1000000000)
                            };
                            
                            document.getElementById('invoiceContent').innerHTML = generateInvoice(paymentData);
                            document.getElementById('invoiceSection').classList.remove('hidden');
                            
                            // Complete timeline
                            updateTimeline(4);
                            
                        } catch (error) {
                            console.error('Error updating payment status:', error);
                            updateStatusUI('red', 'Update Error', 'Payment processed but failed to update status. Please check your bookings page.');
                        }
                        
                    } else if (result === 'pending') {
                        updateStatusUI('blue', 'Still Processing', 'Please check your phone and enter your M-Pesa PIN to complete the payment.');
                        showRetryButton();
                    } else if (result === 'failed') {
                        updateStatusUI('red', 'Payment Failed', 'Transaction failed due to insufficient funds. Please top up and try again.');
                        showRetryButton();
                    } else if (result === 'cancelled') {
                        updateStatusUI('yellow', 'Payment Cancelled', 'You cancelled the transaction. Please try again if you wish to complete the payment.');
                        showRetryButton();
                    }
                    
                    resetPayButton();
                }
            }, 1000);
        });

        function resetPayButton() {
            const payButton = document.getElementById('payButton');
            payButton.disabled = false;
            payButton.innerHTML = `
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Pay KSh {{ number_format($booking->total_amount, 2) }} via M-Pesa
            `;
        }

        function updateStatusUI(color, title, message) {
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');
            
            statusTitle.textContent = title;
            statusMessage.textContent = message;
        }

        function showRetryButton() {
            const paymentStatus = document.getElementById('paymentStatus');
            let retryButton = document.getElementById('retryButton');

            if (!retryButton) {
                retryButton = document.createElement('button');
                retryButton.id = 'retryButton';
                retryButton.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Try Again
                `;
                retryButton.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors duration-200 flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-blue-200 mt-4';
                retryButton.onclick = () => {
                    document.getElementById('paymentStatus').classList.add('hidden');
                    retryButton.remove();
                    document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
                };
                paymentStatus.appendChild(retryButton);
            }
        }
    </script>
</body>
</html>