class PaymentHandler {
    constructor() {
        this.pollingInterval = null;
        this.maxPollingTime = 300000; // 5 minutes
        this.startTime = null;
    }

    initiatePayment(formElement, bookingId) {
        const formData = new FormData(formElement);

        // Show loading state
        this.setLoadingState(true);

        fetch(`/student/payment/${bookingId}/initiate`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSuccessMessage(data.message);
                this.startPolling(data.payment_id, data.checkout_request_id);
            } else {
                this.showErrorMessage(data.message);
                this.setLoadingState(false);
            }
        })
        .catch(error => {
            console.error('Payment initiation error:', error);
            this.showErrorMessage('An error occurred while initiating payment.');
            this.setLoadingState(false);
        });
    }

    startPolling(paymentId, checkoutRequestId) {
        this.startTime = Date.now();
        this.pollingInterval = setInterval(() => {
            this.checkPaymentStatus(paymentId);
        }, 5000); // Check every 5 seconds

        // Stop polling after max time
        setTimeout(() => {
            this.stopPolling();
            this.showErrorMessage('Payment timeout. Please check your phone or try again.');
        }, this.maxPollingTime);
    }

    checkPaymentStatus(paymentId) {
        fetch('/student/payment/check-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ payment_id: paymentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'successful') {
                    this.stopPolling();
                    this.showSuccessMessage(data.message);
                    // Redirect to success page or update UI
                    setTimeout(() => {
                        window.location.href = '/student/my-bookings';
                    }, 2000);
                } else if (data.status === 'failed' || data.status === 'cancelled') {
                    this.stopPolling();
                    this.showErrorMessage(data.message);
                }
                // If still pending, continue polling
            } else {
                console.error('Status check failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
        });
    }

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    setLoadingState(loading) {
        const submitBtn = document.querySelector('#payment-submit-btn');
        const originalText = submitBtn.dataset.originalText || 'Make Payment';

        if (loading) {
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
        } else {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showMessage(message, type) {
        // Implement your notification system here
        alert(message); // Replace with toast notification
    }
}

// Initialize payment handler
const paymentHandler = new PaymentHandler();
