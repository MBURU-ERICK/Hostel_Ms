<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\MpesaService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show payment page for a booking
     */
    public function showPaymentForm($bookingId)
    {
        $booking = Booking::with('hostel')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        // Check if booking is eligible for payment
        if (!in_array($booking->booking_status, ['confirmed', 'pending', 'approved'])) {
            return redirect()->route('student.my-bookings')
                ->with('error', 'This booking is not ready for payment. Current status: ' . $booking->booking_status);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('student.my-bookings')
                ->with('info', 'This booking has already been paid for.');
        }

        // Check if there's a pending payment
        $pendingPayment = Payment::where('booking_id', $booking->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingPayment) {
            return view('student.payment', compact('booking', 'pendingPayment'));
        }

        return view('student.payment', compact('booking'));
    }

    /**
     * Initiate M-Pesa payment (with development mode support)
     */
    public function initiatePayment(Request $request, $bookingId, MpesaService $mpesaService)
    {
        try {
            $request->validate([
                'phone_number' => 'required|string',
            ]);

            $booking = Booking::with('hostel')
                ->where('user_id', Auth::id())
                ->findOrFail($bookingId);

            // Validate booking status
            if (!in_array($booking->booking_status, ['confirmed', 'pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is not ready for payment. Current status: ' . $booking->booking_status
                ], 400);
            }

            if ($booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking has already been paid for.'
                ], 400);
            }

            // Check for existing pending payment
            $existingPayment = Payment::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($existingPayment && $existingPayment->created_at->gt(now()->subMinutes(30))) {
                return response()->json([
                    'success' => false,
                    'message' => 'A payment is already pending for this booking. Please complete or wait for it to expire.',
                    'payment_id' => $existingPayment->id
                ], 400);
            }

            // Check if in development mode
            $isDevelopment = $request->input('simulation', false) || app()->environment('local');
            $simulationType = $request->input('simulation_type', 'success');

            // Format phone number
            $phoneNumber = $this->formatPhoneNumber($request->phone_number);
            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format. Please use format: 0712345678 or 712345678'
                ], 400);
            }

            if ($isDevelopment) {
                // Development mode - create payment record but don't call actual M-Pesa
                return $this->handleDevelopmentPayment($booking, $phoneNumber, $simulationType);
            }

            // Production mode - use actual M-Pesa
            return $this->handleProductionPayment($booking, $phoneNumber, $mpesaService);

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle development mode payment
     */
    private function handleDevelopmentPayment($booking, $phoneNumber, $simulationType)
    {
        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'phone_number' => $phoneNumber,
                'payment_method' => 'mpesa',
                'status' => 'pending',
                'initiated_at' => now(),
                'merchant_request_id' => 'DEV' . strtoupper(uniqid()),
                'checkout_request_id' => 'DEV' . strtoupper(uniqid()),
                'response_code' => '0',
                'result_description' => 'Development mode payment initiated',
            ]);

            // Log the payment initiation
            Log::info('Development payment initiated', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'simulation_type' => $simulationType,
                'phone' => $phoneNumber
            ]);

            DB::commit();

            // Broadcast payment initiation event (for real-time updates)
            $this->broadcastPaymentEvent('payment.initiated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'simulation_type' => $simulationType
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated in development mode',
                'payment_id' => $payment->id,
                'checkout_request_id' => $payment->checkout_request_id,
                'development_mode' => true,
                'simulation_type' => $simulationType,
                'booking' => [
                    'id' => $booking->id,
                    'amount' => $booking->total_amount,
                    'hostel' => $booking->hostel->name
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Development payment creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment record. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle production mode payment with actual M-Pesa
     */
    private function handleProductionPayment($booking, $phoneNumber, $mpesaService)
    {
        DB::beginTransaction();
        
        try {
            // Validate and format amount for M-Pesa
            $amount = intval(round($booking->total_amount));
            if ($amount <= 0) {
                throw new \Exception('Invalid payment amount');
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'phone_number' => $phoneNumber,
                'payment_method' => 'mpesa',
                'status' => 'pending',
                'initiated_at' => now(),
            ]);

            // Initiate STK Push
            $result = $mpesaService->initiateSTKPush(
                $phoneNumber,
                $amount,
                'HOSTEL' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'Hostel Booking - ' . $booking->hostel->name
            );

            if ($result['success']) {
                // Update payment with request IDs
                $payment->update([
                    'merchant_request_id' => $result['merchant_request_id'],
                    'checkout_request_id' => $result['checkout_request_id'],
                    'response_code' => '0',
                    'result_description' => $result['response_description'],
                ]);

                DB::commit();

                Log::info('M-Pesa payment initiated', [
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $result['checkout_request_id']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['customer_message'] ?? 'Payment request sent to your phone. Please enter your M-Pesa PIN.',
                    'checkout_request_id' => $result['checkout_request_id'],
                    'payment_id' => $payment->id,
                ]);
            }

            // Payment initiation failed
            $payment->update([
                'status' => 'failed',
                'result_description' => $result['message'],
                'completed_at' => now(),
            ]);

            DB::commit();

            Log::warning('M-Pesa payment initiation failed', [
                'payment_id' => $payment->id,
                'message' => $result['message']
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to initiate payment. Please try again.',
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production payment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Update payment status (for development mode and callback handling)
     */
    public function updatePaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'status' => 'required|in:pending,successful,failed,cancelled',
                'transaction_id' => 'nullable|string',
                'completed_at' => 'nullable|date',
                'result_description' => 'nullable|string'
            ]);

            $booking = Booking::with('hostel', 'user')
                ->where('user_id', Auth::id())
                ->findOrFail($request->booking_id);

            $payment = Payment::where('booking_id', $booking->id)
                ->latest()
                ->first();

            if (!$payment) {
                // Create payment record if it doesn't exist
                $payment = Payment::create([
                    'user_id' => Auth::id(),
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_amount,
                    'phone_number' => '254712345678',
                    'payment_method' => 'mpesa',
                    'status' => $request->status,
                    'initiated_at' => now()->subMinutes(5),
                    'completed_at' => $request->completed_at ?? now(),
                    'transaction_id' => $request->transaction_id ?? 'DEV' . strtoupper(uniqid()),
                    'merchant_request_id' => 'DEV' . strtoupper(uniqid()),
                    'checkout_request_id' => 'DEV' . strtoupper(uniqid()),
                    'result_description' => $request->result_description ?? 'Payment completed in development mode'
                ]);
            } else {
                $payment->update([
                    'status' => $request->status,
                    'completed_at' => $request->completed_at ?? now(),
                    'transaction_id' => $request->transaction_id ?? $payment->transaction_id,
                    'result_description' => $request->result_description ?? $payment->result_description
                ]);
            }

            // Handle successful payment
            if ($request->status === 'successful') {
                $oldPaymentStatus = $booking->payment_status;
                
                $booking->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $payment->amount,
                    'booking_status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);

                // Broadcast payment success to all relevant parties
                $this->broadcastPaymentSuccess($booking, $payment, $oldPaymentStatus);

                // Send notifications
                $this->sendPaymentNotifications($booking, $payment);
            }

            // Handle failed payment
            if ($request->status === 'failed') {
                $this->broadcastPaymentEvent('payment.failed', [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'user_id' => Auth::id(),
                    'reason' => $request->result_description ?? 'Payment failed'
                ]);
            }

            // Handle cancelled payment
            if ($request->status === 'cancelled') {
                $this->broadcastPaymentEvent('payment.cancelled', [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'user_id' => Auth::id()
                ]);
            }

            Log::info('Payment status updated via API', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'new_status' => $request->status,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'transaction_id' => $payment->transaction_id,
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'completed_at' => $payment->completed_at
                ],
                'booking' => [
                    'id' => $booking->id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'confirmed_at' => $booking->confirmed_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get recent payment updates for real-time UI updates
     */
    public function getRecentUpdates(Request $request)
    {
        try {
            $userId = Auth::id();
            $since = $request->get('since', now()->subMinutes(10)->toDateTimeString());
            
            // Get payments updated since the specified time
            $payments = Payment::where('user_id', $userId)
                ->with('booking.hostel')
                ->where('updated_at', '>=', $since)
                ->orderBy('updated_at', 'desc')
                ->get();

            $updates = [];
            foreach ($payments as $payment) {
                $updates[] = [
                    'booking_id' => $payment->booking_id,
                    'payment_id' => $payment->id,
                    'payment_status' => $payment->status,
                    'booking_status' => $payment->booking?->booking_status,
                    'amount' => $payment->amount,
                    'formatted_amount' => 'KSh ' . number_format($payment->amount, 2),
                    'transaction_id' => $payment->transaction_id,
                    'hostel_name' => $payment->booking?->hostel?->name,
                    'updated_at' => $payment->updated_at->toDateTimeString(),
                    'updated_at_human' => $payment->updated_at->diffForHumans(),
                    'completed_at' => $payment->completed_at?->toDateTimeString()
                ];
            }

            // Get booking status updates
            $bookings = Booking::where('user_id', $userId)
                ->where('updated_at', '>=', $since)
                ->whereNotIn('id', $payments->pluck('booking_id'))
                ->get();

            foreach ($bookings as $booking) {
                $updates[] = [
                    'booking_id' => $booking->id,
                    'booking_status' => $booking->booking_status,
                    'payment_status' => $booking->payment_status,
                    'updated_at' => $booking->updated_at->toDateTimeString(),
                    'updated_at_human' => $booking->updated_at->diffForHumans()
                ];
            }

            return response()->json([
                'success' => true,
                'updates' => $updates,
                'timestamp' => now()->toDateTimeString(),
                'count' => count($updates)
            ]);

        } catch (\Exception $e) {
            Log::error('Recent updates error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch updates',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get invoice for a booking
     */
    public function getInvoice($bookingId)
    {
        try {
            $booking = Booking::with(['hostel', 'user', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->where('user_id', Auth::id())
                ->findOrFail($bookingId);

            $payment = $booking->payments()
                ->where('status', 'successful')
                ->latest()
                ->first();

            if (!$payment) {
                // Try to find any payment
                $payment = $booking->payments()->latest()->first();
                
                if (!$payment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No payment found for this booking'
                    ], 404);
                }
            }

            // Generate invoice HTML
            $invoiceHtml = view('partials.invoice', [
                'booking' => $booking,
                'payment' => $payment,
                'user' => Auth::user(),
                'company' => [
                    'name' => 'Hostel Management System',
                    'address' => '123 Hostel Avenue, Nairobi, Kenya',
                    'phone' => '+254 700 000000',
                    'email' => 'payments@hostelmanagement.com',
                    'website' => 'www.hostelmanagement.com'
                ]
            ])->render();

            // Log invoice generation
            Log::info('Invoice generated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'invoice_html' => $invoiceHtml,
                'payment' => [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => $payment->amount,
                    'formatted_amount' => 'KSh ' . number_format($payment->amount, 2),
                    'status' => $payment->status,
                    'completed_at' => $payment->completed_at?->format('F d, Y g:i A')
                ],
                'booking' => [
                    'id' => $booking->id,
                    'reference' => '#' . str_pad($booking->id, 6, '0', STR_PAD_LEFT)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Invoice generation error: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * M-Pesa callback URL for processing payments
     */
    public function mpesaCallback(Request $request)
    {
        // Log all callback attempts
        Log::info('M-Pesa Callback Received:', [
            'method' => $request->method(),
            'data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Reject non-POST requests immediately
        if (!$request->isMethod('post')) {
            Log::warning('Invalid method for M-Pesa callback', [
                'method' => $request->method(),
                'expected' => 'POST'
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Method not allowed. Only POST requests are accepted.'
            ], 405);
        }

        $callbackData = $request->all();

        try {
            if (isset($callbackData['Body']['stkCallback'])) {
                $stkCallback = $callbackData['Body']['stkCallback'];
                $checkoutRequestId = $stkCallback['CheckoutRequestID'];
                $resultCode = $stkCallback['ResultCode'];
                $resultDesc = $stkCallback['ResultDesc'];

                // Find payment by checkout request ID
                $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();

                if ($payment) {
                    DB::beginTransaction();
                    
                    try {
                        if ($resultCode == 0) {
                            // Payment successful
                            $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

                            $transactionData = [];
                            foreach ($callbackMetadata as $item) {
                                $transactionData[$item['Name']] = $item['Value'] ?? null;
                            }

                            $mpesaReceipt = $transactionData['MpesaReceiptNumber'] ?? 
                                           $transactionData['ReceiptNumber'] ?? 
                                           'MP' . rand(100000, 999999);

                            $oldStatus = $payment->status;
                            
                            $payment->update([
                                'status' => 'successful',
                                'transaction_id' => $mpesaReceipt,
                                'result_code' => $resultCode,
                                'result_description' => $resultDesc,
                                'completed_at' => now(),
                            ]);

                            $booking = $payment->booking;
                            if ($booking) {
                                $oldPaymentStatus = $booking->payment_status;
                                
                                $booking->update([
                                    'payment_status' => 'paid',
                                    'amount_paid' => $payment->amount,
                                    'booking_status' => 'confirmed',
                                    'confirmed_at' => now(),
                                ]);

                                // Broadcast success
                                $this->broadcastPaymentSuccess($booking, $payment, $oldPaymentStatus);
                                
                                // Send notifications
                                $this->sendPaymentNotifications($booking, $payment);
                            }

                            Log::info('Payment processed successfully via callback', [
                                'payment_id' => $payment->id,
                                'transaction_id' => $mpesaReceipt,
                                'booking_id' => $payment->booking_id,
                                'old_status' => $oldStatus
                            ]);
                        } else {
                            // Payment failed
                            $payment->update([
                                'status' => 'failed',
                                'result_code' => $resultCode,
                                'result_description' => $resultDesc,
                                'completed_at' => now(),
                            ]);

                            Log::warning('Payment failed via callback', [
                                'payment_id' => $payment->id,
                                'result_code' => $resultCode,
                                'result_desc' => $resultDesc
                            ]);

                            // Broadcast failure
                            $this->broadcastPaymentEvent('payment.failed', [
                                'booking_id' => $payment->booking_id,
                                'payment_id' => $payment->id,
                                'user_id' => $payment->user_id,
                                'reason' => $resultDesc
                            ]);
                        }
                        
                        DB::commit();
                        
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                } else {
                    Log::warning('Payment not found for callback', [
                        'checkout_request_id' => $checkoutRequestId
                    ]);
                }
            } else {
                Log::warning('Invalid callback structure received', [
                    'callback_data' => $callbackData
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing M-Pesa callback', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData,
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Always return success to M-Pesa
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Show payment history
     */
    public function paymentHistory(Request $request)
    {
        $query = Payment::byUser(auth()->id())
            ->with('booking.hostel');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalSpent = Payment::successful()
            ->byUser(auth()->id())
            ->sum('amount');

        $stats = [
            'total_payments' => Payment::byUser(auth()->id())->count(),
            'successful_payments' => Payment::successful()->byUser(auth()->id())->count(),
            'total_spent' => $totalSpent,
            'average_payment' => Payment::successful()->byUser(auth()->id())->avg('amount') ?? 0,
            'last_payment' => Payment::successful()->byUser(auth()->id())->latest()->first()
        ];

        return view('student.payment-history', compact('payments', 'stats'));
    }

    /**
     * Retry failed payment
     */
    public function retryPayment(Request $request, $paymentId, MpesaService $mpesaService)
    {
        try {
            $payment = Payment::where('user_id', Auth::id())
                ->with('booking.hostel')
                ->findOrFail($paymentId);

            if (!$payment->canRetry()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment cannot be retried. It may be too old or already completed.'
                ], 400);
            }

            $booking = $payment->booking;

            // Check if booking is still valid
            if (!$booking || $booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking has already been paid for.'
                ], 400);
            }

            // Create new payment record
            $newPayment = Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => $payment->booking_id,
                'amount' => $payment->amount,
                'phone_number' => $payment->phone_number,
                'payment_method' => 'mpesa',
                'status' => 'pending',
                'initiated_at' => now(),
            ]);

            // Check if in development mode
            if (app()->environment('local')) {
                $newPayment->update([
                    'merchant_request_id' => 'DEV' . strtoupper(uniqid()),
                    'checkout_request_id' => 'DEV' . strtoupper(uniqid()),
                    'response_code' => '0',
                    'result_description' => 'Development mode payment retry',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment retry initiated in development mode',
                    'payment_id' => $newPayment->id,
                    'development_mode' => true
                ]);
            }

            // Production mode - initiate STK Push
            $result = $mpesaService->initiateSTKPush(
                $payment->phone_number,
                intval(round($payment->amount)),
                'HOSTEL' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'Hostel Booking - ' . $booking->hostel->name . ' (Retry)'
            );

            if ($result['success']) {
                $newPayment->update([
                    'merchant_request_id' => $result['merchant_request_id'],
                    'checkout_request_id' => $result['checkout_request_id'],
                    'response_code' => '0',
                    'result_description' => $result['response_description'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $result['customer_message'] ?? 'Payment request sent to your phone.',
                    'checkout_request_id' => $result['checkout_request_id'],
                    'payment_id' => $newPayment->id,
                ]);
            }

            $newPayment->update([
                'status' => 'failed',
                'result_description' => $result['message'],
                'completed_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to initiate payment.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment retry error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrying the payment.'
            ], 500);
        }
    }

    /**
     * Get payment details
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $payment = Payment::where('user_id', Auth::id())
                ->with(['booking.hostel', 'booking.user'])
                ->findOrFail($paymentId);

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'status' => $payment->status,
                    'status_badge' => $payment->status_badge,
                    'phone_number' => $payment->formatted_phone,
                    'transaction_id' => $payment->transaction_id,
                    'method' => $payment->payment_method,
                    'initiated_at' => $payment->initiated_at?->format('F d, Y g:i A'),
                    'completed_at' => $payment->completed_at?->format('F d, Y g:i A'),
                    'duration' => $payment->duration,
                    'booking' => [
                        'id' => $payment->booking->id,
                        'reference' => '#' . str_pad($payment->booking->id, 6, '0', STR_PAD_LEFT),
                        'hostel' => $payment->booking->hostel->name
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment details'
            ], 500);
        }
    }

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Request $request, $paymentId)
    {
        try {
            $payment = Payment::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->findOrFail($paymentId);

            $payment->update([
                'status' => 'cancelled',
                'result_description' => $request->reason ?? 'Cancelled by user',
                'completed_at' => now(),
            ]);

            Log::info('Payment cancelled by user', [
                'payment_id' => $payment->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Cancel payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment'
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats()
    {
        try {
            $userId = Auth::id();
            
            $stats = [
                'total_spent' => Payment::successful()->byUser($userId)->sum('amount'),
                'total_payments' => Payment::byUser($userId)->count(),
                'successful_payments' => Payment::successful()->byUser($userId)->count(),
                'failed_payments' => Payment::failed()->byUser($userId)->count(),
                'pending_payments' => Payment::pending()->byUser($userId)->count(),
                'average_payment' => Payment::successful()->byUser($userId)->avg('amount') ?? 0,
                'last_month_spent' => Payment::successful()
                    ->byUser($userId)
                    ->where('completed_at', '>=', now()->subMonth())
                    ->sum('amount'),
                'most_expensive_payment' => Payment::successful()
                    ->byUser($userId)
                    ->orderBy('amount', 'desc')
                    ->first()?->amount ?? 0
            ];

            // Format numbers
            $stats['formatted_total_spent'] = 'KSh ' . number_format($stats['total_spent'], 2);
            $stats['formatted_average_payment'] = 'KSh ' . number_format($stats['average_payment'], 2);
            $stats['formatted_last_month_spent'] = 'KSh ' . number_format($stats['last_month_spent'], 2);
            $stats['formatted_most_expensive'] = 'KSh ' . number_format($stats['most_expensive_payment'], 2);

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Payment stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment statistics'
            ], 500);
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^\d]/', '', $phone);
        
        // Check if it's a valid Kenyan number
        if (preg_match('/^(0|7|1)\d{8}$/', $phone)) {
            // Remove leading 0 if present
            $phone = ltrim($phone, '0');
            return '254' . $phone;
        }
        
        // Check if it's already in international format
        if (preg_match('/^254[71]\d{8}$/', $phone)) {
            return $phone;
        }
        
        return null;
    }

    /**
     * Broadcast payment event (for real-time updates)
     */
    private function broadcastPaymentEvent($event, $data)
    {
        // In development, just log the broadcast
        Log::info('Broadcasting payment event', [
            'event' => $event,
            'data' => $data,
            'timestamp' => now()->toDateTimeString()
        ]);

        // In production, you would implement actual broadcasting here
        // e.g., using Laravel Broadcasting, Pusher, WebSockets, etc.
        
        // You could also store in session for the next page load
        session()->flash('payment_update', [
            'event' => $event,
            'data' => $data,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Broadcast payment success to all relevant parties
     */
    private function broadcastPaymentSuccess($booking, $payment, $oldStatus)
    {
        $updateData = [
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'user_id' => $booking->user_id,
            'landlord_id' => $booking->hostel->user_id,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
            'amount' => $payment->amount,
            'formatted_amount' => 'KSh ' . number_format($payment->amount, 2),
            'transaction_id' => $payment->transaction_id,
            'completed_at' => $payment->completed_at?->toDateTimeString(),
            'old_status' => $oldStatus,
            'timestamp' => now()->toDateTimeString()
        ];

        // Broadcast to student
        $this->broadcastPaymentEvent('payment.success.student', $updateData);
        
        // Broadcast to landlord
        $this->broadcastPaymentEvent('payment.success.landlord', [
            'booking_id' => $booking->id,
            'student_name' => $booking->user->name,
            'hostel_name' => $booking->hostel->name,
            'amount' => $payment->amount,
            'formatted_amount' => 'KSh ' . number_format($payment->amount, 2),
            'transaction_id' => $payment->transaction_id,
            'completed_at' => $payment->completed_at?->toDateTimeString()
        ]);
        
        // Broadcast to admin
        $this->broadcastPaymentEvent('payment.success.admin', [
            'booking_id' => $booking->id,
            'student_id' => $booking->user_id,
            'student_name' => $booking->user->name,
            'landlord_id' => $booking->hostel->user_id,
            'landlord_name' => $booking->hostel->user->name,
            'hostel_name' => $booking->hostel->name,
            'amount' => $payment->amount,
            'formatted_amount' => 'KSh ' . number_format($payment->amount, 2),
            'transaction_id' => $payment->transaction_id
        ]);

        Log::info('Payment success broadcasted', $updateData);
    }

    /**
     * Send payment notifications
     */
    private function sendPaymentNotifications($booking, $payment)
    {
        try {
            // Send email to student
            // Mail::to($booking->user->email)->send(new PaymentConfirmation($booking, $payment));
            
            // Send SMS to student (if using SMS service)
            // $this->smsService->send($booking->user->phone, 'Payment received: KSh ' . $payment->amount);
            
            // Send notification to landlord
            // Notification::send($booking->hostel->user, new PaymentReceived($booking, $payment));
            
            // Log notifications
            Log::info('Payment notifications sent', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'student_notified' => true,
                'landlord_notified' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment notifications: ' . $e->getMessage());
        }
    }

    /**
     * Check payment status (for polling)
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payments,id'
            ]);

            $payment = Payment::where('user_id', Auth::id())
                ->with('booking')
                ->findOrFail($request->payment_id);

            return response()->json([
                'success' => true,
                'status' => $payment->status,
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'status_badge' => $payment->status_badge,
                    'transaction_id' => $payment->transaction_id,
                    'completed_at' => $payment->completed_at?->toDateTimeString(),
                    'result_description' => $payment->result_description
                ],
                'booking' => $payment->booking ? [
                    'id' => $payment->booking->id,
                    'payment_status' => $payment->booking->payment_status,
                    'booking_status' => $payment->booking->booking_status
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error('Check payment status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }

    /**
     * Admin: Get all payments (for admin dashboard)
     */
    public function adminGetAllPayments(Request $request)
    {
        // Ensure user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access.');
        }

        $query = Payment::with(['user', 'booking.hostel']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by user or transaction
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_revenue' => Payment::successful()->sum('amount'),
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::successful()->count(),
            'pending_payments' => Payment::pending()->count(),
            'failed_payments' => Payment::failed()->count(),
            'today_revenue' => Payment::successful()
                ->whereDate('completed_at', today())
                ->sum('amount'),
            'this_month_revenue' => Payment::successful()
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('amount')
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Landlord: Get payments for their hostels
     */
    public function landlordGetPayments(Request $request)
    {
        $landlordId = Auth::id();

        $query = Payment::whereHas('booking.hostel', function($q) use ($landlordId) {
                $q->where('user_id', $landlordId);
            })
            ->with(['user', 'booking.hostel']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by hostel
        if ($request->has('hostel_id')) {
            $query->whereHas('booking', function($q) use ($request) {
                $q->where('hostel_id', $request->hostel_id);
            });
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalEarned = Payment::successful()
            ->whereHas('booking.hostel', function($q) use ($landlordId) {
                $q->where('user_id', $landlordId);
            })
            ->sum('amount');

        return view('landlord.payments.index', compact('payments', 'totalEarned'));
    }
}