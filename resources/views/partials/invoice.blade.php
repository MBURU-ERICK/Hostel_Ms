<div class="space-y-6">
    <!-- Invoice Header -->
    <div class="flex justify-between items-start border-b border-gray-200 pb-4">
        <div>
            <h4 class="text-2xl font-bold text-gray-900">INVOICE</h4>
            <p class="text-sm text-gray-600 mt-1">#{{ $payment->transaction_id ?? 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Date: {{ $payment->completed_at?->format('F d, Y') ?? now()->format('F d, Y') }}</p>
            <p class="text-sm text-gray-600">Time: {{ $payment->completed_at?->format('g:i A') ?? now()->format('g:i A') }}</p>
        </div>
    </div>
    
    <!-- Company & Customer Details -->
    <div class="flex justify-between items-start">
        <div>
            <p class="font-semibold text-gray-900">Hostel Management System</p>
            <p class="text-sm text-gray-600">123 Hostel Avenue</p>
            <p class="text-sm text-gray-600">Nairobi, Kenya</p>
            <p class="text-sm text-gray-600">Tel: +254 700 000000</p>
            <p class="text-sm text-gray-600">Email: payments@hostelmanagement.com</p>
        </div>
        <div class="text-right">
            <p class="font-semibold text-gray-900">Bill To:</p>
            <p class="text-sm text-gray-600">{{ $user->name }}</p>
            <p class="text-sm text-gray-600">{{ $user->email }}</p>
            <p class="text-sm text-gray-600">{{ $payment->formatted_phone }}</p>
        </div>
    </div>
    
    <!-- Payment Details Table -->
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
                    <td class="py-2 px-3">
                        <span class="font-medium">{{ $booking->hostel->name }}</span>
                        <br>
                        <span class="text-gray-500 text-xs">Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="text-right py-2 px-3 font-medium">{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-2 px-3 text-gray-600">Room Type: {{ $booking->room_type ?? 'Standard' }}</td>
                    <td class="text-right py-2 px-3"></td>
                </tr>
                <tr>
                    <td class="py-2 px-3 text-gray-600">Duration: {{ $booking->duration_months }} month(s)</td>
                    <td class="text-right py-2 px-3"></td>
                </tr>
                <tr>
                    <td class="py-2 px-3 text-gray-600">Check-in Date: {{ $booking->check_in_date->format('M d, Y') }}</td>
                    <td class="text-right py-2 px-3"></td>
                </tr>
                @if($booking->special_requests)
                <tr>
                    <td class="py-2 px-3 text-gray-600" colspan="2">
                        <span class="font-medium">Special Requests:</span>
                        <br>
                        <span class="text-xs">{{ $booking->special_requests }}</span>
                    </td>
                </tr>
                @endif
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td class="py-3 px-3">Total Amount Paid</td>
                    <td class="text-right py-3 px-3 text-green-600 text-lg">KSh {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Payment Information Grid -->
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-gray-600">Payment Method:</p>
            <p class="font-semibold text-gray-900">M-Pesa {{ $payment->payment_method === 'mpesa' ? '(Mobile Money)' : '' }}</p>
        </div>
        <div>
            <p class="text-gray-600">Transaction ID:</p>
            <p class="font-semibold text-gray-900">{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-gray-600">M-Pesa Receipt:</p>
            <p class="font-semibold text-gray-900">{{ $payment->merchant_request_id ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-gray-600">Payment Status:</p>
            <p class="font-semibold text-green-600">Paid</p>
        </div>
        <div>
            <p class="text-gray-600">Phone Number:</p>
            <p class="font-semibold text-gray-900">{{ $payment->formatted_phone }}</p>
        </div>
        <div>
            <p class="text-gray-600">Payment Date:</p>
            <p class="font-semibold text-gray-900">{{ $payment->completed_at?->format('M d, Y g:i A') }}</p>
        </div>
    </div>
    
    <!-- Booking Confirmation -->
    <div class="border-t border-gray-200 pt-4 mt-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800 font-medium">
                    Your booking has been confirmed! Check-in date: {{ $booking->check_in_date->format('l, F d, Y') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="border-t border-gray-200 pt-4 mt-4 text-center text-sm text-gray-500">
        <p>Thank you for choosing Hostel Management System!</p>
        <p class="mt-1">This invoice serves as your official payment receipt.</p>
        <p class="mt-3 text-xs">For any queries regarding this payment, please contact our support team at support@hostelmanagement.com or call +254 700 000000</p>
    </div>
    
    <!-- Terms -->
    <div class="text-xs text-gray-400 text-center pt-2">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Invoice generated on {{ now()->format('F d, Y \\a\\t g:i A') }}</p>
    </div>
</div>