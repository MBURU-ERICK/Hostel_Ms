<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Approved - HostelHub</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; color: white; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Account Approved!</h1>
            <p>Welcome to HostelHub</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }},</h2>

            <p>Great news! Your {{ $user->user_type }} account has been approved by our administration team.</p>

            <p>You can now access all the features available for your account type:</p>

            @if($user->user_type === 'landlord')
            <ul>
                <li>🏠 List and manage your hostels</li>
                <li>📊 View booking requests</li>
                <li>💬 Communicate with students</li>
                <li>🔧 Request maintenance services</li>
            </ul>
            @elseif($user->user_type === 'service_provider')
            <ul>
                <li>🔧 View service requests</li>
                <li>📝 Manage your service profile</li>
                <li>⭐ Build your reputation with reviews</li>
                <li>💰 Set your service rates</li>
            </ul>
            @endif

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Access Your Dashboard</a>
            </div>

            <p><strong>Next Steps:</strong></p>
            <ol>
                <li>Login to your account</li>
                <li>Complete your profile setup</li>
                <li>Start using the platform features</li>
            </ol>

            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

            <p>Best regards,<br>
            The HostelHub Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} HostelHub. All rights reserved.</p>
            <p>
                <a href="{{ url('/') }}">Visit Website</a> |
                <a href="mailto:support@hostelhub.com">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>
