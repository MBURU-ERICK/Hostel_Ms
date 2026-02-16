<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Successful</title>
</head>
<body>
    <h2>Payment Received Successfully</h2>

    <p>Hello {{ $user->name }},</p>

    <p>Your payment of <strong>KSh {{ number_format($payment->amount, 2) }}</strong> has been received successfully.</p>

    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
        <h3>Booking Details:</h3>
        <p><strong>Hostel:</strong> {{ $booking->hostel->name }}</p>
        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
        <p><strong>Amount Paid:</strong> KSh {{ number_format($payment->amount, 2) }}</p>
        <p><strong>Payment Date:</strong> {{ $payment->completed_at->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <p>Thank you for choosing our service!</p>
</body>
</html>
