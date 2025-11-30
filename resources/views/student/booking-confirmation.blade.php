<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Booking Confirmation - Hostel Management System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
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
            </div>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="bg-green-600 px-6 py-8 text-center">
                    <svg class="w-16 h-16 text-white mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h1 class="text-2xl font-bold text-white">Booking Request Submitted!</h1>
                    <p class="text-green-100 mt-2">Your booking request has been received and is pending confirmation.</p>
                </div>

                <!-- Booking Details -->
                <div class="p-6 space-y-6">
                    <!-- Booking Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Booking Details</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Booking ID:</span>
                                    <span class="font-mono font-semibold">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Hostel:</span>
                                    <span class="font-semibold">{{ $booking->hostel->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-semibold">{{ $booking->hostel->location }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in Date:</span>
                                    <span class="font-semibold">{{ $booking->check_in_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-semibold">{{ $booking->duration_months }} month{{ $booking->duration_months > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-semibold capitalize {{ $booking->booking_status === 'confirmed' ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $booking->booking_status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Monthly Rent:</span>
                                    <span>KSh {{ number_format($booking->hostel->rent_per_month) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Security Deposit:</span>
                                    <span>KSh {{ number_format($booking->hostel->deposit_amount) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-lg border-t pt-2">
                                    <span>Total Amount:</span>
                                    <span class="text-blue-600">KSh {{ number_format($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Section -->
                    @if($booking->booking_status === 'confirmed')
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Status</h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($booking->payment_status === 'paid')
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-green-700 font-semibold">Payment Completed</span>
                                @elseif($booking->payment_status === 'pending')
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-yellow-700 font-semibold">Payment Pending</span>
                                @else
                                    <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Awaiting Confirmation</span>
                                @endif
                            </div>

                            @if($booking->payment_status === 'pending')
                                <div class="text-sm text-gray-600">
                                    Amount Due: <span class="font-semibold text-blue-600">KSh {{ number_format($booking->total_amount) }}</span>
                                </div>
                            @endif
                        </div>

                        @if($booking->payment_status === 'pending')
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-700">
                                💡 <strong>Quick Tip:</strong> Complete your payment now to secure your booking.
                                You'll be redirected to our secure M-Pesa payment gateway.
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Status Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900">What happens next?</h4>
                                <div class="text-blue-700 text-sm mt-2 space-y-1">
                                    @if($booking->booking_status === 'pending')
                                        <p>• The landlord will review your booking request within 24 hours</p>
                                        <p>• You'll receive a confirmation email once approved</p>
                                        <p>• After approval, you can proceed with payment</p>
                                    @elseif($booking->booking_status === 'confirmed' && $booking->payment_status === 'pending')
                                        <p>• Your booking has been confirmed by the landlord</p>
                                        <p>• Please complete the payment to secure your room</p>
                                        <p>• Payment can be made via M-Pesa</p>
                                    @elseif($booking->payment_status === 'paid')
                                        <p>• Your booking and payment are complete</p>
                                        <p>• The landlord will contact you for check-in details</p>
                                        <p>• Welcome to your new hostel!</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        @if($booking->booking_status === 'confirmed' && $booking->payment_status === 'pending')
                            <!-- Show Payment Button if booking is confirmed but payment is pending -->
                            <a href="{{ route('student.payment.make', $booking->id) }}"
                               class="flex-1 bg-green-600 text-white text-center py-3 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-semibold flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Make Payment Now
                            </a>
                        @elseif($booking->payment_status === 'paid')
                            <!-- Show Paid Status -->
                            <div class="flex-1 bg-green-100 border border-green-300 text-green-700 text-center py-3 px-4 rounded-lg font-semibold flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Payment Completed
                            </div>
                        @else
                            <!-- Show Pending Confirmation -->
                            <div class="flex-1 bg-yellow-100 border border-yellow-300 text-yellow-700 text-center py-3 px-4 rounded-lg font-semibold flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Waiting for Landlord Confirmation
                            </div>
                        @endif

                        <a href="{{ route('student.my-bookings') }}"
                           class="flex-1 bg-blue-600 text-white text-center py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-semibold">
                            View My Bookings
                        </a>
                        <a href="{{ route('student.dashboard') }}"
                           class="flex-1 bg-gray-200 text-gray-700 text-center py-3 px-4 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 font-semibold">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
