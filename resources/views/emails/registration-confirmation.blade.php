<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Received - HostelHub</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 30px; border: 1px solid #e9ecef; border-top: none; border-radius: 0 0 8px 8px; }
        .info-box { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Registration Received</h1>
            <p>Thank you for joining HostelHub!</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }},</h2>

            <p>Thank you for registering as a <strong>{{ $user->user_type }}</strong> on HostelHub.</p>

            <div class="info-box">
                <h3>📋 Account Details:</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Account Type:</strong> {{ ucfirst($user->user_type) }}</p>
                <p><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y') }}</p>
            </div>

            @if(in_array($user->user_type, ['landlord', 'service_provider']))
            <div class="info-box">
                <h3>⏳ Approval Process</h3>
                <p>Your account is currently under review by our administration team. This process typically takes 24-48 hours.</p>
                <p>You will receive another email once your account has been approved.</p>
            </div>
            @else
            <div class="info-box">
                <h3>✅ Account Activated</h3>
                <p>Your student account has been activated immediately. You can start using all features right away!</p>
            </div>
            @endif

            <p>If you have any questions during this process, please contact our support team.</p>

            <p>Best regards,<br>
            The HostelHub Team</p>
        </div>
    </div>
</body>
</html>
