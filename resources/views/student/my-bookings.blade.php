<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Bookings - Hostel Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
                        <span class="ml-2 text-xl font-bold text-gray-900">Hostel Management</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Bookings</h1>
                <p class="text-gray-600 mt-2">Manage your hostel bookings and reservations</p>
            </div>




            <!-- Bookings List -->
            @if($bookings->count() > 0)
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->hostel->name }}</h3>
                                        <p class="text-gray-600">{{ $booking->hostel->location }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                               ($booking->booking_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                               ($booking->booking_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($booking->booking_status) }}
                                        </span>
                                        <div class="text-lg font-bold text-blue-600 mt-1">
                                            KSh {{ number_format($booking->total_amount) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <span class="text-sm text-gray-600">Booking ID:</span>
                                        <p class="font-mono font-semibold">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Check-in Date:</span>
                                        <p class="font-semibold">{{ $booking->check_in_date->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Duration:</span>
                                        <p class="font-semibold">{{ $booking->duration_months }} month{{ $booking->duration_months > 1 ? 's' : '' }}</p>
                                    </div>
                                </div>

                                @if($booking->special_requests)
                                    <div class="mb-4">
                                        <span class="text-sm text-gray-600">Special Requests:</span>
                                        <p class="text-gray-700">{{ $booking->special_requests }}</p>
                                    </div>
                                @endif

                                @if($booking->cancellation_reason)
                                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                                        <span class="text-sm font-medium text-red-800">Cancellation Reason:</span>
                                        <p class="text-red-700 mt-1">{{ $booking->cancellation_reason }}</p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('student.view-hostel', $booking->hostel->id) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        View Hostel
                                    </a>

                                    <!-- Message Landlord Button -->
                                    @if(in_array($booking->booking_status, ['pending', 'confirmed', 'approved']))
                                        <a href="{{ route('messages.index', $booking->id) }}"
                                           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                            Message Landlord
                                        </a>
                                    @endif

                                    @if($booking->canBeCancelled())
                                        <button onclick="openCancelModal({{ $booking->id }})"
                                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel Booking
                                        </button>
                                    @endif

                                    @if($booking->booking_status === 'confirmed' && $booking->payment_status === 'pending')
                                        <a href="{{ route('student.payment.make', $booking->id) }}"
                                           class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            Make Payment
                                        </a>
                                    @endif
                                </div>

                                <!-- Booking Dates -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-500">
                                        Booked on: {{ $booking->created_at->format('M d, Y \\a\\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h3>
                    <p class="text-gray-500 mb-6">Start exploring hostels and make your first booking!</p>
                    <a href="{{ route('student.search-hostels') }}"
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        Browse Hostels
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Cancel Booking</h3>
            <form id="cancelForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for cancellation
                    </label>
                    <textarea name="cancellation_reason" id="cancellation_reason"
                              rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal(bookingId) {
            const form = document.getElementById('cancelForm');
            form.action = `/student/booking/${bookingId}/cancel`;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    </script>
</body>
</html>
