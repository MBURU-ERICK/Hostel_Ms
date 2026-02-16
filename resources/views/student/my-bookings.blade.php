<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Bookings - Hostel Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
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
        }
        
        @keyframes status-pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .status-updated {
            animation: status-pulse 1s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Development Mode Badge -->
    <div class="dev-badge">
        🚀 DEVELOPMENT MODE - Real-time Updates Active
    </div>

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
                    <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
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

            <!-- Real-time Update Notification (hidden by default) -->
            <div id="updateNotification" class="hidden mb-4 bg-green-50 border border-green-200 rounded-lg p-4 animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800" id="notificationMessage">
                            Payment status updated! Refreshing data...
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            @if($bookings->count() > 0)
                <div class="space-y-6" id="bookingsList">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden booking-item" 
                             data-booking-id="{{ $booking->id }}"
                             data-payment-status="{{ $booking->payment_status }}"
                             data-booking-status="{{ $booking->booking_status }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $booking->hostel->name }}</h3>
                                        <p class="text-gray-600">{{ $booking->hostel->location }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge
                                            {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                               ($booking->booking_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                               ($booking->booking_status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                               ($booking->booking_status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                               'bg-gray-100 text-gray-800'))) }}">
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

                                <!-- Payment Status -->
                                <div class="mb-4">
                                    <span class="text-sm text-gray-600">Payment Status:</span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge
                                        {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                           ($booking->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($booking->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 
                                           ($booking->payment_status === 'refunded' ? 'bg-purple-100 text-purple-800' : 
                                           'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                    
                                    @if($booking->payment_status === 'paid' && $booking->amount_paid)
                                        <span class="ml-2 text-sm text-green-600">
                                            Paid: KSh {{ number_format($booking->amount_paid) }}
                                        </span>
                                    @endif
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
                                        <a href="{{ route('student.payment.form', $booking->id) }}"
                                           class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            Make Payment
                                        </a>
                                    @endif

                                    @if($booking->payment_status === 'paid')
                                        <button onclick="viewInvoice({{ $booking->id }})"
                                                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            View Invoice
                                        </button>
                                    @endif
                                </div>

                                <!-- Booking Dates -->
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm text-gray-500">
                                        Booked on: {{ $booking->created_at->format('M d, Y \\a\\t g:i A') }}
                                    </p>
                                    @if($booking->confirmed_at)
                                        <p class="text-sm text-gray-500">
                                            Confirmed on: {{ $booking->confirmed_at->format('M d, Y \\a\\t g:i A') }}
                                        </p>
                                    @endif
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

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Payment Invoice</h3>
                <button onclick="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6" id="invoiceModalContent">
                <!-- Invoice content will be loaded here -->
            </div>
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
                <button onclick="downloadInvoice()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Invoice
                </button>
                <button onclick="closeInvoiceModal()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Check for payment updates on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkForPaymentUpdates();
            
            // Set up polling for real-time updates (every 10 seconds)
            setInterval(checkForPaymentUpdates, 10000);
            
            // Listen for storage events (for cross-tab updates)
            window.addEventListener('storage', function(e) {
                if (e.key === 'payment_update') {
                    handlePaymentUpdate(JSON.parse(e.newValue));
                }
            });
        });

        // Check for payment updates via API
        async function checkForPaymentUpdates() {
            try {
                const response = await fetch('/student/payment/recent-updates', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.updates && data.updates.length > 0) {
                    data.updates.forEach(update => {
                        updateBookingStatus(update);
                    });
                    
                    // Show notification
                    showUpdateNotification('Payment status updated!');
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
            }
        }

        // Update booking status in UI
        function updateBookingStatus(update) {
            const bookingElement = document.querySelector(`.booking-item[data-booking-id="${update.booking_id}"]`);
            
            if (bookingElement) {
                // Update payment status badge
                const paymentBadge = bookingElement.querySelector('.payment-status-badge');
                if (paymentBadge) {
                    paymentBadge.textContent = ucfirst(update.payment_status);
                    paymentBadge.className = getPaymentBadgeClass(update.payment_status);
                }
                
                // Update booking status badge
                const bookingBadge = bookingElement.querySelector('.booking-status-badge');
                if (bookingBadge && update.booking_status) {
                    bookingBadge.textContent = ucfirst(update.booking_status);
                    bookingBadge.className = getBookingBadgeClass(update.booking_status);
                }
                
                // Highlight updated booking
                bookingElement.classList.add('status-updated');
                setTimeout(() => {
                    bookingElement.classList.remove('status-updated');
                }, 2000);
                
                // Update payment button if needed
                updateActionButtons(bookingElement, update);
            }
        }

        function getPaymentBadgeClass(status) {
            const classes = {
                'paid': 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge bg-green-100 text-green-800',
                'pending': 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge bg-yellow-100 text-yellow-800',
                'failed': 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge bg-red-100 text-red-800',
                'refunded': 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge bg-purple-100 text-purple-800'
            };
            return classes[status] || 'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium payment-status-badge bg-gray-100 text-gray-800';
        }

        function getBookingBadgeClass(status) {
            const classes = {
                'confirmed': 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge bg-green-100 text-green-800',
                'pending': 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge bg-yellow-100 text-yellow-800',
                'cancelled': 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge bg-red-100 text-red-800',
                'approved': 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge bg-blue-100 text-blue-800'
            };
            return classes[status] || 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium booking-status-badge bg-gray-100 text-gray-800';
        }

        function updateActionButtons(bookingElement, update) {
            const actionButtons = bookingElement.querySelector('.flex.flex-wrap.gap-2');
            
            // If payment is now paid, remove payment button and add invoice button
            if (update.payment_status === 'paid') {
                const paymentButton = actionButtons.querySelector('a[href*="payment"]');
                if (paymentButton) {
                    paymentButton.remove();
                    
                    // Add invoice button if not present
                    if (!actionButtons.querySelector('button[onclick*="viewInvoice"]')) {
                        const invoiceButton = createInvoiceButton(update.booking_id);
                        actionButtons.appendChild(invoiceButton);
                    }
                }
            }
        }

        function createInvoiceButton(bookingId) {
            const button = document.createElement('button');
            button.onclick = () => viewInvoice(bookingId);
            button.className = 'bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm font-medium flex items-center';
            button.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                View Invoice
            `;
            return button;
        }

        function showUpdateNotification(message) {
            const notification = document.getElementById('updateNotification');
            const messageEl = document.getElementById('notificationMessage');
            
            messageEl.textContent = message;
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 5000);
        }

        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Cancel booking functions
        function openCancelModal(bookingId) {
            const form = document.getElementById('cancelForm');
            form.action = `/student/booking/${bookingId}/cancel`;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // Invoice functions
        async function viewInvoice(bookingId) {
            try {
                const response = await fetch(`/student/booking/${bookingId}/invoice`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('invoiceModalContent').innerHTML = data.invoice_html;
                    document.getElementById('invoiceModal').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading invoice:', error);
                alert('Failed to load invoice. Please try again.');
            }
        }

        function closeInvoiceModal() {
            document.getElementById('invoiceModal').classList.add('hidden');
        }

        function downloadInvoice() {
            const invoiceContent = document.getElementById('invoiceModalContent').innerHTML;
            const style = `
                <style>
                    body { font-family: Arial, sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
                </style>
            `;
            const fullHtml = `
                <!DOCTYPE html>
                <html>
                <head><title>Payment Invoice</title>${style}</head>
                <body>
                    ${invoiceContent}
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

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        document.getElementById('invoiceModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInvoiceModal();
            }
        });
    </script>
</body>
</html>