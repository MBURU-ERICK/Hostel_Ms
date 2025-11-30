<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

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

        if (!in_array($booking->booking_status, ['confirmed', 'pending'])) {
            return redirect()->route('student.my-bookings')
                ->with('error', 'This booking is not ready for payment.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('student.my-bookings')
                ->with('info', 'This booking has already been paid for.');
        }

        return view('student.payment', compact('booking'));
    }

    /**
     * Initiate M-Pesa payment
     */
    public function initiatePayment(Request $request, $bookingId, DarajaService $darajaService)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $booking = Booking::with('hostel')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        // Validate booking status
        if (!in_array($booking->booking_status, ['confirmed', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not ready for payment.'
            ], 400);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Booking has already been paid for.'
            ], 400);
        }

        // Validate and format phone number
        $formattedPhone = $darajaService->validatePhoneNumber($request->phone_number);
        if (!$formattedPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format. Please use 2547XXXXXXXX format.'
            ], 400);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'phone_number' => $formattedPhone,
            'payment_method' => 'mpesa',
            'status' => 'pending',
            'initiated_at' => now(),
        ]);

        // Initiate STK Push
        $result = $darajaService->initiateSTKPush(
            $formattedPhone,
            $booking->total_amount,
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

            return response()->json([
                'success' => true,
                'message' => $result['customer_message'] ?? 'Payment request sent to your phone. Please enter your M-Pesa PIN.',
                'checkout_request_id' => $result['checkout_request_id'],
                'payment_id' => $payment->id,
            ]);
        }

        // Update payment as failed
        $payment->update([
            'status' => 'failed',
            'result_description' => $result['message'],
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to initiate payment. Please try again.',
        ], 400);
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request, DarajaService $darajaService)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::where('user_id', Auth::id())
            ->with('booking')
            ->findOrFail($request->payment_id);

        if (!$payment->checkout_request_id) {
            return response()->json([
                'success' => false,
                'message' => 'No payment request found.'
            ], 400);
        }

        // Check transaction status from Daraja
        $result = $darajaService->checkTransactionStatus($payment->checkout_request_id);

        if ($result['success']) {
            $resultCode = $result['result_code'];
            $statusDescription = $darajaService->getStatusDescription($resultCode);

            if ($resultCode == '0') {
                // Payment successful
                $payment->update([
                    'status' => 'successful',
                    'transaction_id' => $result['response_data']['MpesaReceiptNumber'] ?? null,
                    'result_code' => $resultCode,
                    'result_description' => $statusDescription,
                    'completed_at' => now(),
                ]);

                // Update booking payment status
                $payment->booking->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $payment->amount,
                    'booking_status' => 'confirmed',
                ]);

                // Send notifications
                try {
                    NotificationService::notifyPaymentSuccess($payment);
                    NotificationService::notifyLandlordPaymentReceived($payment);
                } catch (\Exception $e) {
                    Log::error('Failed to send payment notifications: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'status' => 'successful',
                    'message' => 'Payment completed successfully! Your booking is now confirmed.',
                    'transaction_id' => $payment->transaction_id,
                ]);
            } elseif (in_array($resultCode, ['1032', '1037', '1', '2001'])) {
                // Payment cancelled or failed
                $paymentStatus = in_array($resultCode, ['1032', '1037']) ? 'cancelled' : 'failed';

                $payment->update([
                    'status' => $paymentStatus,
                    'result_code' => $resultCode,
                    'result_description' => $statusDescription,
                    'completed_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $paymentStatus,
                    'message' => $statusDescription,
                ]);
            } else {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'result_code' => $resultCode,
                    'result_description' => $statusDescription,
                    'completed_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'failed',
                    'message' => $statusDescription,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to check payment status. Please try again.',
        ], 400);
    }

    /**
     * M-Pesa callback URL for processing payments
     */
    /**
 * M-Pesa callback URL for processing payments
 */
public function mpesaCallback(Request $request, DarajaService $darajaService)
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
        // Your existing callback processing logic here
        if (isset($callbackData['Body']['stkCallback'])) {
            $stkCallback = $callbackData['Body']['stkCallback'];
            $checkoutRequestId = $stkCallback['CheckoutRequestID'];
            $resultCode = $stkCallback['ResultCode'];
            $resultDesc = $stkCallback['ResultDesc'];

            // Find payment by checkout request ID
            $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();

            if ($payment) {
                if ($resultCode == 0) {
                    // Payment successful - process as before
                    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

                    $transactionData = [];
                    foreach ($callbackMetadata as $item) {
                        $transactionData[$item['Name']] = $item['Value'] ?? null;
                    }

                    $mpesaReceipt = $transactionData['MpesaReceiptNumber'] ?? $transactionData['ReceiptNumber'] ?? null;

                    $payment->update([
                        'status' => 'successful',
                        'transaction_id' => $mpesaReceipt,
                        'result_code' => $resultCode,
                        'result_description' => $resultDesc,
                        'completed_at' => now(),
                    ]);

                    $payment->booking->update([
                        'payment_status' => 'paid',
                        'amount_paid' => $payment->amount,
                        'booking_status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);

                    // Send notifications
                    try {
                        NotificationService::notifyPaymentSuccess($payment);
                        NotificationService::notifyLandlordPaymentReceived($payment);
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment notifications: ' . $e->getMessage());
                    }

                    Log::info('Payment processed successfully via callback', [
                        'payment_id' => $payment->id,
                        'transaction_id' => $mpesaReceipt,
                        'booking_id' => $payment->booking_id
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
            'callback_data' => $callbackData
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
    public function paymentHistory()
    {
        $payments = Payment::with(['booking.hostel'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.payment-history', compact('payments'));
    }

    /**
     * Retry failed payment
     */
    public function retryPayment(Request $request, $paymentId, DarajaService $darajaService)
    {
        $payment = Payment::where('user_id', Auth::id())
            ->with('booking.hostel')
            ->findOrFail($paymentId);

        if (!$payment->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'This payment cannot be retried.'
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

        // Initiate STK Push
        $result = $darajaService->initiateSTKPush(
            $payment->phone_number,
            $payment->amount,
            'HOSTEL' . str_pad($payment->booking_id, 6, '0', STR_PAD_LEFT),
            'Hostel Booking - ' . $payment->booking->hostel->name
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
    }
}
