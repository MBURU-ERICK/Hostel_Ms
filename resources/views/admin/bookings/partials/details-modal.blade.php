<div class="space-y-6">
    <!-- Booking Header -->
    <div class="flex justify-between items-start border-b border-gray-200 pb-4">
        <div>
            <h4 class="text-lg font-semibold text-gray-900">Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h4>
            <p class="text-sm text-gray-600">Created: {{ $booking->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <div>
            @php
                $statusColors = [
                    'confirmed' => 'bg-green-100 text-green-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                    'approved' => 'bg-purple-100 text-purple-800'
                ];
                $statusColor = $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                {{ ucfirst($booking->booking_status) }}
            </span>
        </div>
    </div>

    <!-- Student Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Student Information
        </h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Name</p>
                <p class="font-medium">{{ $booking->user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Email</p>
                <p class="font-medium">{{ $booking->user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Phone</p>
                <p class="font-medium">{{ $booking->user->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Student ID</p>
                <p class="font-medium">#{{ str_pad($booking->user->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
    </div>

    <!-- Hostel Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Hostel Information
        </h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Hostel Name</p>
                <p class="font-medium">{{ $booking->hostel->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Location</p>
                <p class="font-medium">{{ $booking->hostel->location }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Landlord</p>
                <p class="font-medium">{{ $booking->hostel->landlord->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Contact</p>
                <p class="font-medium">{{ $booking->hostel->contact_phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Booking Details
        </h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500">Check-in Date</p>
                <p class="font-medium">{{ $booking->check_in_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Duration</p>
                <p class="font-medium">{{ $booking->duration }} months</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Amount</p>
                <p class="font-medium text-green-600">KSh {{ number_format($booking->total_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Amount Paid</p>
                <p class="font-medium {{ $booking->amount_paid ? 'text-green-600' : 'text-gray-600' }}">
                    KSh {{ number_format($booking->amount_paid ?? 0, 2) }}
                </p>
            </div>
            @if($booking->special_requests)
            <div class="col-span-2">
                <p class="text-xs text-gray-500">Special Requests</p>
                <p class="font-medium">{{ $booking->special_requests }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            Payment History
        </h5>
        
        @if($booking->payments->count() > 0)
            <div class="space-y-3">
                @foreach($booking->payments as $payment)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">
                                    @if($payment->status == 'successful')
                                        <span class="text-green-600">Payment Successful</span>
                                    @elseif($payment->status == 'pending')
                                        <span class="text-yellow-600">Payment Pending</span>
                                    @elseif($payment->status == 'failed')
                                        <span class="text-red-600">Payment Failed</span>
                                    @else
                                        <span class="text-gray-600">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    Amount: KSh {{ number_format($payment->amount, 2) }}
                                </p>
                                @if($payment->transaction_id)
                                    <p class="text-xs text-gray-500">
                                        Transaction: {{ $payment->transaction_id }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500">
                                    Date: {{ $payment->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($payment->status == 'successful') bg-green-100 text-green-800
                                @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payment->status == 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-3">No payments found for this booking.</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Close
        </button>
        @if($booking->booking_status == 'pending')
            <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="booking_status" value="confirmed">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Confirm Booking
                </button>
            </form>
        @endif
    </div>
</div>