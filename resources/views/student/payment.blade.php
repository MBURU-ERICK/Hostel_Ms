<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Make Payment - Hostel Management System</title>

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

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('student.my-bookings') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        ← Back to Bookings
                    </a>
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="bg-green-600 px-6 py-6 text-center">
                    <h1 class="text-2xl font-bold text-white">Make Payment</h1>
                    <p class="text-green-100 mt-2">Complete your booking payment via M-Pesa</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Details</h2>

                        <!-- Booking Summary -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Booking Summary</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Hostel:</span>
                                    <span class="font-semibold">{{ $booking->hostel->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span>{{ $booking->duration_months }} month{{ $booking->duration_months > 1 ? 's' : '' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Check-in:</span>
                                    <span>{{ $booking->check_in_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-lg border-t pt-2">
                                    <span>Total Amount:</span>
                                    <span class="text-blue-600">KSh {{ number_format((float) ($booking->total_amount ?? 0)) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form id="paymentForm">
                            @csrf

                            <div class="mb-6">
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    M-Pesa Phone Number
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        +254
                                    </span>
                                    <input type="text" id="phone_number" name="phone_number" required
                                        pattern="[0-9]{9}"
                                        placeholder="712345678"
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        value="{{ old('phone_number', Auth::user()->phone ? substr(Auth::user()->phone, -9) : '') }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Enter your M-Pesa registered phone number without +254</p>
                            </div>

                            <button type="submit" id="payButton"
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-semibold flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Pay KSh {{ number_format((float) ($booking->total_amount ?? 0)) }} via M-Pesa
                            </button>
                        </form>

                        <!-- Payment Status -->
                        <div id="paymentStatus" class="hidden mt-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="font-semibold text-blue-900" id="statusTitle">Processing Payment</h4>
                                        <p class="text-blue-700 text-sm mt-1" id="statusMessage">
                                            A payment request has been sent to your phone. Please enter your M-Pesa PIN to complete the payment.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Countdown Timer -->
                            <div class="mt-4 text-center">
                                <div class="text-sm text-gray-600">Checking payment status in <span id="countdown">60</span> seconds</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div id="progressBar" class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Instructions</h3>
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5">1.</div>
                                <p class="ml-2">Enter your M-Pesa registered phone number</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5">2.</div>
                                <p class="ml-2">Click "Pay via M-Pesa" button</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5">3.</div>
                                <p class="ml-2">Check your phone for STK Push notification</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5">4.</div>
                                <p class="ml-2">Enter your M-Pesa PIN when prompted</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-5 w-5 text-green-500 mt-0.5">5.</div>
                                <p class="ml-2">Wait for payment confirmation</p>
                            </div>
                        </div>
                    </div>

                    <!-- Help Information -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Ensure you have sufficient funds in your M-Pesa account</li>
                                        <li>Keep your phone nearby to enter your PIN</li>
                                        <li>Do not close this page until payment is complete</li>
                                        <li>Contact support if you encounter any issues</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const payButton = document.getElementById('payButton');
            const paymentStatus = document.getElementById('paymentStatus');
            const phoneInput = document.getElementById('phone_number');

            // Validate phone number
            const phoneNumber = '254' + phoneInput.value;
            if (!phoneInput.value.match(/^[0-9]{9}$/)) {
                alert('Please enter a valid 9-digit phone number (without +254)');
                return;
            }

            // Disable pay button and show loading
            payButton.disabled = true;
            payButton.innerHTML = `
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Processing...
            `;

            // Make API request
            fetch(`/student/payment/{{ $booking->id }}/initiate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone_number: phoneNumber
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show payment status
                    paymentStatus.classList.remove('hidden');
                    startPaymentStatusCheck(data.payment_id);
                } else {
                    alert(data.message || 'Failed to initiate payment. Please try again.');
                    payButton.disabled = false;
                    payButton.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Pay KSh {{ number_format((float) ($booking->total_amount ?? 0)) }} via M-Pesa
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                payButton.disabled = false;
                payButton.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Pay KSh {{ number_format((float) ($booking->total_amount ?? 0)) }} via M-Pesa
                `;
            });
        });

        function startPaymentStatusCheck(paymentId) {
            let countdown = 60;
            const countdownElement = document.getElementById('countdown');
            const progressBar = document.getElementById('progressBar');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');

            const interval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                progressBar.style.width = `${(countdown / 60) * 100}%`;

                if (countdown <= 0) {
                    clearInterval(interval);
                    checkPaymentStatus(paymentId);
                }
            }, 1000);

            function checkPaymentStatus(paymentId) {
                fetch('/student/payment/check-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_id: paymentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'successful') {
                            // Payment successful
                            statusTitle.textContent = 'Payment Successful!';
                            statusTitle.className = 'font-semibold text-green-900';
                            statusMessage.textContent = data.message;
                            statusMessage.className = 'text-green-700 text-sm mt-1';

                            // Update progress bar to green
                            progressBar.className = 'bg-green-600 h-2 rounded-full';

                            // Redirect to bookings page after 3 seconds
                            setTimeout(() => {
                                window.location.href = '{{ route("student.my-bookings") }}';
                            }, 3000);

                        } else if (data.status === 'cancelled') {
                            // Payment cancelled
                            statusTitle.textContent = 'Payment Cancelled';
                            statusTitle.className = 'font-semibold text-yellow-900';
                            statusMessage.textContent = data.message;
                            statusMessage.className = 'text-yellow-700 text-sm mt-1';
                            progressBar.className = 'bg-yellow-600 h-2 rounded-full';

                            // Show retry button
                            showRetryButton(paymentId);

                        } else if (data.status === 'failed') {
                            // Payment failed
                            statusTitle.textContent = 'Payment Failed';
                            statusTitle.className = 'font-semibold text-red-900';
                            statusMessage.textContent = data.message;
                            statusMessage.className = 'text-red-700 text-sm mt-1';
                            progressBar.className = 'bg-red-600 h-2 rounded-full';

                            // Show retry button
                            showRetryButton(paymentId);

                        } else {
                            // Still pending, check again in 5 seconds
                            setTimeout(() => {
                                checkPaymentStatus(paymentId);
                            }, 5000);
                        }
                    } else {
                        // Error checking status
                        statusTitle.textContent = 'Error';
                        statusTitle.className = 'font-semibold text-red-900';
                        statusMessage.textContent = data.message || 'Failed to check payment status. Please refresh the page.';
                        statusMessage.className = 'text-red-700 text-sm mt-1';
                        progressBar.className = 'bg-red-600 h-2 rounded-full';
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                    statusTitle.textContent = 'Connection Error';
                    statusTitle.className = 'font-semibold text-red-900';
                    statusMessage.textContent = 'Failed to connect to server. Please check your internet connection.';
                    statusMessage.className = 'text-red-700 text-sm mt-1';
                    progressBar.className = 'bg-red-600 h-2 rounded-full';
                });
            }

            // Initial check
            checkPaymentStatus(paymentId);
        }

        function showRetryButton(paymentId) {
            const paymentStatus = document.getElementById('paymentStatus');
            const retryButton = document.createElement('button');

            retryButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Retry Payment
            `;

            retryButton.className = 'mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium flex items-center justify-center';

            retryButton.onclick = function() {
                retryPayment(paymentId);
            };

            paymentStatus.appendChild(retryButton);
        }

        function retryPayment(paymentId) {
            const retryButton = document.querySelector('button[onclick*="retryPayment"]');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');
            const progressBar = document.getElementById('progressBar');
            const countdownElement = document.getElementById('countdown');

            // Show loading state
            retryButton.disabled = true;
            retryButton.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Retrying...
            `;

            statusTitle.textContent = 'Retrying Payment';
            statusTitle.className = 'font-semibold text-blue-900';
            statusMessage.textContent = 'Initiating new payment request...';
            statusMessage.className = 'text-blue-700 text-sm mt-1';
            progressBar.className = 'bg-blue-600 h-2 rounded-full';
            progressBar.style.width = '100%';
            countdownElement.textContent = '60';

            fetch(`/student/payment/${paymentId}/retry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusMessage.textContent = data.message || 'Payment request sent to your phone. Please enter your M-Pesa PIN.';
                    startPaymentStatusCheck(data.payment_id);

                    // Remove retry button
                    if (retryButton.parentNode) {
                        retryButton.parentNode.removeChild(retryButton);
                    }
                } else {
                    statusTitle.textContent = 'Retry Failed';
                    statusTitle.className = 'font-semibold text-red-900';
                    statusMessage.textContent = data.message || 'Failed to retry payment. Please try again.';
                    statusMessage.className = 'text-red-700 text-sm mt-1';

                    // Reset retry button
                    retryButton.disabled = false;
                    retryButton.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Retry Payment
                    `;
                }
            })
            .catch(error => {
                console.error('Error retrying payment:', error);
                statusTitle.textContent = 'Retry Failed';
                statusTitle.className = 'font-semibold text-red-900';
                statusMessage.textContent = 'Failed to connect to server. Please try again.';
                statusMessage.className = 'text-red-700 text-sm mt-1';

                // Reset retry button
                retryButton.disabled = false;
                retryButton.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Retry Payment
                `;
            });
        }
    </script>
</body>
</html>
